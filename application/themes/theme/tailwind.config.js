/** @type {import('tailwindcss').Config} */
import plugin from "tailwindcss/plugin";

module.exports = {
	content: [
		// "../../**/*.php"
		"./src/scss/**/*.scss",
		"./elements/**/*.php",
		"../../blocks/**/*.php",
		"../../elements/**/*.php",
		"../../single_pages/**/*.php",
		"../../page_templates/**/*.php",
	],
	theme: {
		extend: {
			screens: {
				xxl: "1600px",
				xl: "1279px",
				xmd: "991px",
			},
			fontFamily: {
				matter: ["Matter", "sans-serif"],
			},
			boxShadow: {
				header: "0 4px 9px 0 rgba(0, 0, 0, 0.1)",
			},
			borderRadius: { sm: "1rem", md: "5.5rem", lg: "8rem" },
			colors: {
				"red-berry": "rgba(128, 21, 26, 1)",
				"red-berry-50": "rgba(128, 21, 26, 0.5)",
				"rustic-red": "rgba(26, 10, 12, 1)",
				"rustic-red-50": "rgba(26, 10, 12, 0.5)",
				"rustic-red-20": "rgba(26, 10, 12, 0.2)",
				romance: "rgba(242, 241, 239, 1)",
				black: "rgba(0, 0, 0, 1)",
				grey: "rgba(51, 51, 51, 1)",
				white: "rgba(255, 255, 255, 1)",
				"c-blue": "rgba(10, 102, 194, 1)",
			},
			keyframes: {
				"slide-duration": {
					from: { width: "0" },
					to: { width: "100%" },
				},
				pulse: {
					"0%": {
						opacity: 0,
					},
					50: {
						opacity: 0.1,
					},
					100: {
						opacity: 0,
					},
				},
			},
			animation: {
				"slide-duration": "slide-duration 8s linear",
				pulse: "pulse 2.5s infinite",
			},
			typography: ({ theme }) => ({
				DEFAULT: {
					css: {
						maxWidth: "100%",
						"--tw-prose-body": theme("colors.black"),
						"--tw-prose-headings": theme("colors.black"),
						"--tw-prose-lead": theme("colors.black"),
						"--tw-prose-links": theme("colors.red-berry"),
						"--tw-prose-bold": theme("colors.black"),
						"--tw-prose-bullets": theme("colors.black"),
						"--tw-prose-hr": theme("colors.black"),
					},
				},
			}),
		},
	},
	plugins: [
		require("@tailwindcss/typography"),
		require("tailwindcss-flip"),
		plugin(({ addComponents, addBase, theme, addVariant, matchVariant }) => {
			addVariant("active", "&.active", "&.swiper-slide-active"),
				addBase({
					body: {
						color: theme("colors.red-berry"),
						background: theme("colors.white"),
						fontFamily: theme("fontFamily.matter"),
						margin: "0",
						fontWeight: "normal",
						fontSmoothing: "antialiased",
						"-webkit-font-smoothing": "antialiased",
						"-moz-osx-font-smoothing": "grayscale",
						overflowX: "hidden",
					},
				});
			matchVariant("nth", (value) => `&:nth-child(${value})`, {
				values: {
					1: "1",
					2: "2",
					3: "3",
					4: "4",
					5: "5",
					6: "6",
				},
			}),
				addComponents({
					h1: {
						fontSize: "4rem",
						lineHeight: "1",
						letterSpacing: "normal",
						fontWeight: "400",
						letterSpacing: '0px',
						fontFamily: theme("fontFamily.matter"),
						"@media (min-width: 640px)": {
							fontSize: "8rem",
							lineHeight: "1",
						},
						"@media (min-width: 1279px)": {
							fontSize: "11rem",
							lineHeight: "1",
							letterSpacing: "-2px",
						},
					},
					h2: {
						fontSize: "3rem",
						lineHeight: "1",
						fontWeight: "400",
						letterSpacing: "0px",
						fontFamily: theme("fontFamily.matter"),
						"@media (min-width: 640px)": {
							fontSize: "6rem",
						},
						"@media (min-width: 1279px)": {
							fontSize: "8rem",
							letterSpacing: "-2px",
						},
					},
					h3: {
						fontSize: "3rem",
						fontWeight: "400",
						lineHeight: "1",

						fontFamily: theme("fontFamily.matter"),
						"@media (min-width: 1279px)": {
							fontSize: "4rem",
							letterSpacing: "-2px",
						},
					},
					h4: {
						fontSize: "3rem",
						fontWeight: "400",
						letterSpacing: "normal",
						fontFamily: theme("fontFamily.matter"),
						"@media (min-width: 640px)": {
							fontSize: "3rem",
							lineHeight: "1.3",
						},
					},
					h6: {
						fontSize: "1.6rem",
						fontWeight: "300",
						textTransform: "uppercase",
						letterSpacing: "normal",
						fontFamily: theme("fontFamily.matter"),
						"@media (min-width: 640px)": {
							fontSize: "2rem",
							lineHeight: "0.95",
							fontWeight: 400,
						},
					},
					".h1": {
						fontSize: theme("spacing.10"),
						lineHeight: "1",
						letterSpacing: "normal",
						fontWeight: "400",

						fontFamily: theme("fontFamily.matter"),
						"@media (min-width: 640px)": {
							fontSize: theme("spacing.44"),
							lineHeight: "1",
							letterSpacing: "-2px",
						},
					},
					".h2": {
						fontSize: "3rem",
						lineHeight: "1",
						fontWeight: "400",
						letterSpacing: "0px",
						fontFamily: theme("fontFamily.matter"),
						"@media (min-width: 640px)": {
							fontSize: "6rem",
						},
						"@media (min-width: 1279px)": {
							fontSize: "8rem",
							letterSpacing: "-2px",
						},
					},
					".h3": {
						fontSize: "3rem",
						fontWeight: "400",
						lineHeight: "1",

						fontFamily: theme("fontFamily.matter"),
						"@media (min-width: 1279px)": {
							fontSize: "4rem",
							letterSpacing: "-2px",
						},
					},
					".h4": {
						fontSize: "3rem",
						fontWeight: "400",
						letterSpacing: "normal",
						fontFamily: theme("fontFamily.matter"),
						"@media (min-width: 640px)": {
							fontSize: "3rem",
							lineHeight: "1.3",
						},
					},
					".h6": {
						fontSize: "1.6rem",
						fontWeight: "300",
						textTransform: "uppercase",
						letterSpacing: "normal",
						fontFamily: theme("fontFamily.matter"),
						"@media (min-width: 640px)": {
							fontSize: "2rem",
							lineHeight: "0.95",
							fontWeight: 400,
						},
					},
					".h15": {
						fontSize: "4rem",
						lineHeight: "1",
						fontWeight: "300",
						fontFamily: theme("fontFamily.matter"),
						"@media (min-width: 991px)": {
							fontSize: "8rem",
						},
						"@media (min-width: 1279px)": {
							fontSize: "15rem",
							letterSpacing: "-2px",
						},
					},
					".text-1": {
						fontSize: "1rem",
						fontWeight: "300",
						lineHeight: "1.2",
						letterSpacing: "normal",
					},
					".text-2": {
						fontSize: "2rem",
						fontWeight: "400",
						lineHeight: "1.2",
						letterSpacing: "normal",
					},
					".text-3": {
						fontSize: "1.6rem",
						fontWeight: "300",
						lineHeight: "1.2",
						letterSpacing: "0.5px",
					},
					p: {
						fontSize: "1.8rem",
						lineHeight: "1.3",
						fontWeight: "300",

					},
					".custom-li": {
						fontSize: "1.8rem",
						lineHeight: "1.3",
						fontWeight: "300",
					},
					a: {
						textDecoration: "none",
						cursor: "pointer",
					},
					".descripter": {
						fontSize: "1.4rem",
						fontWeight: "400",
						lineHeight: "1.3",

						textTransform: "uppercase",
						"@media (min-width: 640px)": {
							fontSize: "1.6rem",
							letterSpacing: "5px",
						},
					},
					input: {
						outline: "none",
					},
					".fade-up, .fade-up-stagger": {
						opacity: 0,
						transform: "translateY(2rem)",
					},
					".fade-left": {
						opacity: 0,
						transform: "translateX(-6rem)",
					},
					".fade-right": {
						opacity: 0,
						transform: "translateX(6rem)",
					},
					".scale-in": {
						transform: "scale(0)",
					},
					".header-transition": {
						transition: "transform .6s ease-out, background .6s ease-out",
					},
					".blurred-img": {
						backgroundRepeat: "no-repeat",
						backgroundSize: "cover",
					},
					".blurred-img::before": {
						content: "",
						position: "absolute",
						inset: 0,
						opacity: 0,
						animation: theme("animation.pulse"),
					},
					".blurred-img.loaded::before": {
						animation: "none",
						content: "none",
					},
					".blurred-img img": {
						opacity: 0,
						transition: "opacity 250ms ease-in-out",
					},
					".blurred-img.loaded img": {
						opacity: 1,
					},
					".swiper-button-prev::after": {
						content: "none",
					},
					".swiper-button-next::after": {
						content: "none",
					},
				});
		}),
	],
};
