---
name: Industrial Heritage Modern
colors:
  surface: '#f9f9f6'
  surface-dim: '#dadad7'
  surface-bright: '#f9f9f6'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f4f4f1'
  surface-container: '#eeeeeb'
  surface-container-high: '#e8e8e5'
  surface-container-highest: '#e2e3e0'
  on-surface: '#1a1c1b'
  on-surface-variant: '#404849'
  inverse-surface: '#2f312f'
  inverse-on-surface: '#f1f1ee'
  outline: '#717879'
  outline-variant: '#c0c8c9'
  surface-tint: '#3c6569'
  primary: '#002428'
  on-primary: '#ffffff'
  primary-container: '#0d3b3f'
  on-primary-container: '#7ba5aa'
  inverse-primary: '#a3ced3'
  secondary: '#7a5900'
  on-secondary: '#ffffff'
  secondary-container: '#fecd6d'
  on-secondary-container: '#775600'
  tertiary: '#1c2028'
  on-tertiary: '#ffffff'
  tertiary-container: '#31353e'
  on-tertiary-container: '#9a9da8'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#bfeaef'
  primary-fixed-dim: '#a3ced3'
  on-primary-fixed: '#002022'
  on-primary-fixed-variant: '#234d51'
  secondary-fixed: '#ffdea3'
  secondary-fixed-dim: '#efc061'
  on-secondary-fixed: '#261900'
  on-secondary-fixed-variant: '#5d4200'
  tertiary-fixed: '#dfe2ed'
  tertiary-fixed-dim: '#c3c6d1'
  on-tertiary-fixed: '#181c23'
  on-tertiary-fixed-variant: '#434750'
  background: '#f9f9f6'
  on-background: '#1a1c1b'
  surface-variant: '#e2e3e0'
  deep-teal: '#0D3B3F'
  heritage-gold: '#DAAD50'
  charcoal-textile: '#4B4F58'
  canvas-white: '#FBFBF8'
  pure-white: '#FFFFFF'
typography:
  display-lg:
    fontFamily: Source Serif 4
    fontSize: 48px
    fontWeight: '700'
    lineHeight: 56px
    letterSpacing: -0.02em
  display-lg-mobile:
    fontFamily: Source Serif 4
    fontSize: 36px
    fontWeight: '700'
    lineHeight: 44px
    letterSpacing: -0.01em
  headline-lg:
    fontFamily: Source Serif 4
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
  headline-md:
    fontFamily: Source Serif 4
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-lg:
    fontFamily: Hanken Grotesk
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.05em
  label-md:
    fontFamily: Hanken Grotesk
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
    letterSpacing: 0.08em
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  base: 8px
  container-max: 1280px
  gutter: 24px
  margin-mobile: 16px
  margin-desktop: 64px
  section-gap: 120px
---

## Brand & Style

The design system is built upon the intersection of **Industrial Heritage** and **Modern Tech**. It serves a B2B textile audience that values the tactile history of manufacturing alongside the efficiency of modern digital procurement. The brand personality is authoritative yet innovative, evoking a sense of "Engineered Elegance."

The design style utilizes a refined **Minimalism** blended with **Corporate Modern** sensibilities. It prioritizes high-quality typography and strategic whitespace to reflect the premium nature of the textiles. Visual interest is generated through subtle textures that mimic fabric weaves and a structured, grid-heavy layout that suggests architectural precision. The emotional response should be one of absolute trust, high-end craftsmanship, and technological reliability.

## Colors

This design system employs a sophisticated, low-vibrancy palette that emphasizes depth and quality. 

- **Primary (Deep Teal):** Represents the "Industrial Heritage." It is used for core brand elements, primary navigation, and high-level headers to establish authority.
- **Secondary (Heritage Gold):** A muted, copper-adjacent gold that acts as a textile-inspired accent. It is used sparingly for call-to-actions, status indicators, and premium highlights.
- **Tertiary (Charcoal Textile):** A desaturated gray used for body text and secondary UI elements to maintain high legibility without the harshness of pure black.
- **Neutral (Canvas White):** The primary background color, providing a soft, natural paper-like quality that feels more premium than standard digital white.

The color mode is strictly **Light**, mimicking the environment of a well-lit design studio or architectural firm.

## Typography

The typography strategy pairs a traditional serif for high-level storytelling with a systematic sans-serif for functional data.

- **Headlines (Source Serif 4):** Chosen for its academic and literary weight. It reflects the heritage aspect of the brand. Large display sizes should use tighter letter-spacing to feel more "editorial."
- **Body (Inter):** Used for all long-form content and UI controls. Its high x-height and neutrality ensure readability in data-heavy B2B contexts.
- **Labels (Hanken Grotesk):** A sharp, contemporary sans-serif used for small metadata, buttons, and overlines. These are frequently set in uppercase with increased letter-spacing to denote technical precision.

## Layout & Spacing

The layout follows a **Fixed Grid** philosophy on desktop to maintain a controlled, editorial feel, transitioning to a fluid model on smaller devices.

- **Grid:** A 12-column grid is used for desktop (1280px max-width). In B2B dashboards, an 8-column inner layout is preferred to keep line lengths readable.
- **Rhythm:** A strictly 8px-based spatial system. 
- **Generous Whitespace:** Section vertical spacing is intentionally large (120px+) to allow the high-end textile imagery to breathe and to prevent the UI from feeling "cluttered" or "cheap."
- **Breakpoints:**
  - Mobile (< 768px): 4 columns, 16px margins.
  - Tablet (768px - 1024px): 8 columns, 32px margins.
  - Desktop (> 1024px): 12 columns, 64px margins.

## Elevation & Depth

This design system avoids heavy drop shadows in favor of **Tonal Layers** and **Low-Contrast Outlines**.

- **Surface Levels:** Depth is created by placing white (`#FFFFFF`) cards on the near-white (`#FBFBF8`) canvas. This creates a subtle, sophisticated lift without the "muddiness" of shadows.
- **Borders:** Subtle 1px borders using `#4B4F58` at 10-15% opacity are used to define containers. 
- **Interactive Depth:** Only the primary "Action" cards use a very soft, ambient shadow (15% opacity, 20px blur, 4px Y-offset) upon hover to provide tactile feedback.
- **The "Industrial Line":** Horizontal and vertical hairline rules are used to separate sections, reminiscent of technical blueprints or loom structures.

## Shapes

The shape language is primarily **Soft (Level 1)**. 

To maintain the "Industrial" and "Technical" feel, large corner radii are avoided. Rectilinear forms with slight softening (4px - 8px) suggest precision-cut materials. 

- **Standard Elements:** 4px radius (Buttons, Input Fields).
- **Large Containers:** 8px radius (Cards, Modals).
- **Exceptions:** Purely decorative elements or textile swatches may use 0px (Sharp) corners to emphasize the raw edge of a fabric sample.

## Components

- **Buttons:** Primary buttons use a solid Deep Teal fill with Hanken Grotesk labels in white. Secondary buttons use a Heritage Gold outline with teal text. Interaction states should be a subtle shift in color saturation, never a large change in shape.
- **Input Fields:** Minimalist design with a 1px bottom border that transforms into a full outline on focus. Labels use the `label-md` typography style.
- **Cards:** Used for product listings and fabric samples. They feature a 1px "ghost border" and use the `Canvas White` background. 
- **Chips:** Small, rectangular tags with 2px radius and light gray backgrounds, used for material specs (e.g., "100% Cotton", "Fire Retardant").
- **Textile Swatches:** A specialized component for this system. Swatches must be large, high-resolution, and accompanied by a technical data grid using monospaced numbers.
- **Lists:** Clean, strictly aligned rows separated by hairline dividers. Icons, if used, should be thin-stroke (1px) and monochromatic.