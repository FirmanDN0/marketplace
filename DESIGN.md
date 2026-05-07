---
version: "alpha"
name: "ServeMix Marketplace"
description: "Premium corporate marketplace with luminous blues, glassy overlays, and soft depth."
colors:
  blue-50: "#f4f7fb"
  blue-100: "#e7eff7"
  blue-200: "#c9dbed"
  blue-300: "#9abce0"
  blue-400: "#6598cf"
  blue-500: "#3f7bbb"
  blue-600: "#2a61a0"
  blue-700: "#224d80"
  blue-800: "#1f426b"
  blue-900: "#1e395a"
  blue-950: "#14253e"
  mist-100: "#dde6f0"
  mist-50: "#f0f4f9"
  slate-300: "#cbd5e1"
  slate-400: "#94a3b8"
  white: "#ffffff"
  black: "#000000"
gradients:
  hero: "linear-gradient(-45deg, #14253e, #224d80, #2a61a0, #1e395a)"
  text-highlight: "linear-gradient(135deg, #e7eff7, #ffffff)"
  service-placeholder: "linear-gradient(135deg, #e7eff7 0%, #c9dbed 40%, #dde6f0 70%, #f0f4f9 100%)"
  shimmer: "linear-gradient(90deg, transparent 25%, rgba(255, 255, 255, 0.10) 50%, transparent 75%)"
  card-glow: "linear-gradient(135deg, rgba(42, 97, 160, 0.06) 0%, transparent 50%)"
typography:
  display-hero:
    fontFamily: "Inter, ui-sans-serif, system-ui, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji"
    fontSize: 3.75rem
    fontWeight: 800
    lineHeight: 1.1
  display-lg:
    fontFamily: "Inter, ui-sans-serif, system-ui, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji"
    fontSize: 3rem
    fontWeight: 800
    lineHeight: 1.1
  heading-lg:
    fontFamily: "Inter, ui-sans-serif, system-ui, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji"
    fontSize: 1.875rem
    fontWeight: 700
    lineHeight: 1.25
  heading-md:
    fontFamily: "Inter, ui-sans-serif, system-ui, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji"
    fontSize: 1.5rem
    fontWeight: 700
    lineHeight: 1.3
  body-md:
    fontFamily: "Inter, ui-sans-serif, system-ui, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji"
    fontSize: 1rem
    fontWeight: 400
    lineHeight: 1.6
  body-sm:
    fontFamily: "Inter, ui-sans-serif, system-ui, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji"
    fontSize: 0.875rem
    fontWeight: 400
    lineHeight: 1.5
  caption:
    fontFamily: "Inter, ui-sans-serif, system-ui, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji"
    fontSize: 0.75rem
    fontWeight: 500
    lineHeight: 1.4
  micro:
    fontFamily: "Inter, ui-sans-serif, system-ui, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji"
    fontSize: 0.6875rem
    fontWeight: 500
    lineHeight: 1.3
  quote-mark:
    fontFamily: "Georgia, serif"
    fontSize: 4.5rem
    fontWeight: 400
    lineHeight: 1
rounded:
  sm: 8px
  md: 12px
  lg: 16px
  xl: 24px
  pill: 999px
spacing:
  2xs: 4px
  xs: 8px
  sm: 12px
  md: 16px
  lg: 20px
  xl: 24px
  2xl: 32px
  3xl: 40px
  4xl: 64px
  5xl: 80px
shadows:
  sm: "0 2px 4px 0 rgb(0 0 0 / 0.02)"
  base: "0 4px 6px -1px rgb(0 0 0 / 0.03), 0 2px 4px -2px rgb(0 0 0 / 0.03)"
  md: "0 8px 12px -2px rgb(0 0 0 / 0.04), 0 3px 6px -3px rgb(0 0 0 / 0.04)"
  lg: "0 15px 25px -4px rgb(0 0 0 / 0.04), 0 6px 12px -6px rgb(0 0 0 / 0.04)"
  xl: "0 25px 40px -5px rgb(0 0 0 / 0.05), 0 10px 15px -8px rgb(0 0 0 / 0.05)"
  card-hover: "0 20px 40px -10px rgba(42, 97, 160, 0.12), 0 8px 16px -6px rgba(0, 0, 0, 0.06)"
  category-hover: "0 12px 28px -6px rgba(42, 97, 160, 0.15)"
  testimonial-hover: "0 16px 32px -8px rgba(42, 97, 160, 0.10)"
elevation:
  surface: "{shadows.sm}"
  card: "{shadows.base}"
  raised: "{shadows.md}"
  floating: "{shadows.lg}"
  hero: "{shadows.xl}"
motion:
  duration-fast: 0.3s
  duration-base: 0.5s
  duration-standard: 0.6s
  duration-slow: 0.7s
  duration-slower: 0.8s
  duration-pulse: 2.5s
  duration-shimmer: 3s
  duration-float: 6s
  duration-float-reverse: 7s
  duration-float-slow: 10s
  duration-gradient: 12s
  easing-standard: "ease"
  easing-out: "ease-out"
  easing-in-out: "ease-in-out"
  easing-linear: "linear"
  easing-emphasized: "cubic-bezier(0.4, 0, 0.2, 1)"
  animation-fade-in-up: "fadeInUp 0.7s ease-out both"
  animation-fade-in-down: "fadeInDown 0.6s ease-out both"
  animation-fade-in: "fadeIn 0.6s ease-out both"
  animation-slide-left: "slideInLeft 0.7s ease-out both"
  animation-slide-right: "slideInRight 0.7s ease-out both"
  animation-scale-in: "scaleIn 0.5s ease-out both"
  animation-gradient-shift: "gradientShift 12s ease infinite"
  animation-float: "float 6s ease-in-out infinite"
  animation-float-reverse: "floatReverse 7s ease-in-out infinite"
  animation-float-slow: "floatSlow 10s ease-in-out infinite"
  animation-pulse-glow: "pulseGlow 2.5s ease-in-out infinite"
  animation-shimmer: "shimmer 3s linear infinite"
  animation-count-up: "countUp 0.6s ease-out both"
  transition-reveal: "opacity 0.6s ease-out, transform 0.6s ease-out"
  transition-reveal-scale: "opacity 0.5s ease-out, transform 0.5s ease-out"
  transition-card-hover: "transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease"
---

## Overview
The visual identity is premium and professional, centered on a corporate blue spectrum with luminous gradients and airy white surfaces. The experience feels confident and modern, yet approachable, with subtle glow effects and soft depth that keep the UI polished without feeling heavy.

## Colors
Blues anchor the system, ranging from deep navy for hero backdrops to pale mist tints for gentle surfaces. White and near-white tones create clarity and lift, while cool slate accents appear in secondary chrome and subtle details. Accent hues appear sparingly in category moments to keep focus on the primary blue story.

## Typography
Inter is the primary typeface, chosen for its neutral geometry and high legibility. Large, bold display sizes establish strong hierarchy in hero and CTA moments. Body copy stays calm and readable with moderate line height, while small labels and metadata lean on slightly tighter sizes and medium weight. A serif quote mark is used as a tasteful ornamental detail in testimonial cards.

## Layout
Spacing is generous, with wide gutters and clear separation between sections to create a calm rhythm. A consistent grid supports cards, stats, and category blocks, while larger vertical padding emphasizes key moments like hero and call-to-action sections. Chips, badges, and small controls keep compact spacing to maintain scannability.

## Elevation & Depth
Depth is created through soft ambient shadows and gentle hover lifts rather than heavy drop shadows. Surfaces stay bright and clean, with occasional glass-like overlays and gradient sheens to convey polish. Depth changes are subtle and primarily triggered by hover or section transitions.

## Shapes
Rounded rectangles dominate the shape language, with large radii on cards and panels for friendliness. Pills are used for chips and search inputs, and circular shapes appear for avatars and icon badges. The overall geometry is soft and approachable, never sharp or angular.

## Components
The hero uses a slow-shifting blue gradient, floating shapes, and a shimmer overlay to set a premium tone. Cards are the core building block: white surfaces, light borders, and hover lift paired with a faint blue glow. Category tiles use color-rich icon badges for quick recognition, while CTAs use bold, high-contrast buttons with gentle scaling and glow feedback. Stats and quick-action tiles maintain consistent padding, subtle borders, and clear typographic hierarchy.

## Do's and Don'ts
- Do keep primary backgrounds in the deep blue gradient family for brand moments.
- Do use soft shadows and hover lift to indicate interactivity.
- Do keep surfaces bright and clean with cool mist tints.
- Don't introduce harsh black shadows or heavy borders.
- Don't overuse multi-hue accents outside category or icon moments.
- Don't compress spacing in hero and CTA sections.
