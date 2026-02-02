# HexaGrid Admin Icons & Skeletons

This directory contains professional SVG icons and skeleton previews for the HexaGrid Product Showcase admin interface.

## Layout Type Icons

Modern, clean SVG icons for layout selection:

- **grid.svg** - 2x2 grid icon for Grid layout
- **list.svg** - List icon with bullets for List layout
- **slider.svg** - Carousel/slider icon for Slider layout
- **table.svg** - Table grid icon for Table layout

All icons are:
- Scalable vector graphics (SVG)
- Stroke-based design
- 24x24 viewBox
- Consistent 2px stroke width
- Color-adaptable via CSS

## Layout Variation Skeletons

Wireframe/skeleton previews for layout variations:

- **skeleton-1.svg** - Modern style product card skeleton
- **skeleton-2.svg** - Classic style product card skeleton

These are placeholder skeletons. You can replace them with your actual layout previews.

### Adding New Skeletons

To add more layout variations:

1. Create a new SVG file (e.g., `skeleton-3.svg`)
2. Use a 120x140 viewBox for consistency
3. Use #E0E0E0 fill color for skeleton elements
4. Update `Meta_Box.php` to include the new variation in the `$layout_variations` array

## Usage

These icons are automatically loaded by the Meta_Box.php file and displayed in the admin interface as modern, clickable selection cards.

## Design Guidelines

When creating new icons or skeletons:
- Keep file sizes small (< 2KB)
- Use simple, recognizable shapes
- Maintain consistent styling
- Ensure good contrast and visibility
- Test at different sizes
