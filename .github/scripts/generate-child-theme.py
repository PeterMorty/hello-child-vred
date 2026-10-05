#!/usr/bin/env python3

import argparse
import re
import shutil
import unicodedata
from pathlib import Path


ROOT = Path(__file__).resolve().parents[2]
BUILD_DIR = ROOT / "build"


def make_slug(value):
	ascii_value = unicodedata.normalize("NFKD", value).encode("ascii", "ignore").decode("ascii")
	slug = re.sub(r"[^a-z0-9]+", "-", ascii_value.lower()).strip("-")

	if not slug:
		raise ValueError("The theme name does not produce a valid slug.")

	return slug


def make_php_prefix(slug):
	prefix = slug.replace("-", "_")

	if prefix[0].isdigit():
		prefix = f"theme_{prefix}"

	return prefix


def make_package(slug):
	return "".join(part.capitalize() for part in slug.split("-"))


def replace_once(content, old, new, label):
	if old not in content:
		raise RuntimeError(f"Expected {label} placeholder was not found.")

	return content.replace(old, new, 1)


def parse_fonts(value):
	return [font.strip() for font in value.splitlines() if font.strip()]


def configure_fonts(theme_dir, functions, fonts, slug, php_prefix, package):
	fonts_path = theme_dir / "includes" / "elementor-fonts.php"
	require_line = "require_once get_stylesheet_directory() . '/includes/elementor-fonts.php';"

	if not fonts:
		if require_line not in functions:
			raise RuntimeError("Elementor fonts include was not found.")

		functions = functions.replace(f"\n{require_line}", "", 1)

		if not fonts_path.exists():
			raise RuntimeError("Elementor fonts file was not found.")

		fonts_path.unlink()
		return functions

	content = fonts_path.read_text(encoding="utf-8")
	content = replace_once(content, "@package Child_Theme", f"@package {package}", "fonts package")
	content = content.replace("child_theme_add_elementor_font_group", f"{php_prefix}_add_elementor_font_group")
	content = content.replace("child_theme_add_elementor_fonts", f"{php_prefix}_add_elementor_fonts")
	content = content.replace("'child-theme'", f"'{slug}'")

	font_lines = "\n".join(
		f"\t$fonts[{font!r}] = 'custom_fonts';"
		for font in fonts
	)
	content = re.sub(
		r"\t\$fonts\['Bebas Neue'\] = 'custom_fonts';\n\t\$fonts\['Mona Sans'\] = 'custom_fonts';",
		font_lines,
		content,
		count=1,
	)

	fonts_path.write_text(content, encoding="utf-8")
	return functions


def generate_theme(theme_name, include_woocommerce, fonts):
	theme_name = theme_name.strip()

	if not theme_name:
		raise ValueError("Theme name cannot be empty.")

	slug = make_slug(theme_name)
	php_prefix = make_php_prefix(slug)
	package = make_package(slug)
	theme_dir = BUILD_DIR / slug

	if BUILD_DIR.exists():
		shutil.rmtree(BUILD_DIR)

	theme_dir.mkdir(parents=True)

	for filename in ("functions.php", "style.css", "screenshot.png"):
		shutil.copy2(ROOT / filename, theme_dir / filename)

	for dirname in ("assets", "includes"):
		shutil.copytree(ROOT / dirname, theme_dir / dirname)

	style_path = theme_dir / "style.css"
	style = style_path.read_text(encoding="utf-8")
	style = replace_once(style, "Theme Name: Nombre", f"Theme Name: {theme_name}", "theme name")
	style = replace_once(style, "Text Domain: nombre", f"Text Domain: {slug}", "text domain")
	style_path.write_text(style, encoding="utf-8")

	functions_path = theme_dir / "functions.php"
	functions = functions_path.read_text(encoding="utf-8")
	functions = replace_once(functions, "Nombre child theme functions", f"{theme_name} child theme functions", "functions title")
	functions = replace_once(functions, "@package Nombre", f"@package {package}", "package")
	functions = functions.replace("nombre_enqueue_styles", f"{php_prefix}_enqueue_styles")
	functions = functions.replace("'nombre-woocommerce'", f"'{slug}-woocommerce'")
	functions = functions.replace("'nombre'", f"'{slug}'")

	if not include_woocommerce:
		pattern = re.compile(
			r"\n\twp_enqueue_style\(\n"
			+ rf"\t\t'{re.escape(slug)}-woocommerce',"
			+ r".*?\n\t\);",
			re.DOTALL,
		)
		functions, replacements = pattern.subn("", functions, count=1)

		if replacements != 1:
			raise RuntimeError("WooCommerce enqueue block was not found.")

		woocommerce_css = theme_dir / "assets" / "css" / "woocommerce.css"

		if not woocommerce_css.exists():
			raise RuntimeError("WooCommerce stylesheet was not found.")

		woocommerce_css.unlink()

	functions = configure_fonts(theme_dir, functions, fonts, slug, php_prefix, package)
	functions_path.write_text(functions, encoding="utf-8")

	return slug


def main():
	parser = argparse.ArgumentParser()
	parser.add_argument("--name", required=True)
	parser.add_argument("--woocommerce", choices=("true", "false"), required=True)
	parser.add_argument("--fonts", default="")
	args = parser.parse_args()

	slug = generate_theme(
		args.name,
		args.woocommerce == "true",
		parse_fonts(args.fonts),
	)
	print(slug)


if __name__ == "__main__":
	main()
