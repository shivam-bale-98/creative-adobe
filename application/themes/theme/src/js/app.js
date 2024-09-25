import Animations from "./components/Animations";
import Banner from "./components/Banner";
import Lenis from "@studio-freight/lenis";
import gsap from "gsap";
import Rellax from "rellax";
import Header from "./components/Header";
import Maps from "./components/Maps";
import BlockMain from "./Blocks/BlockMain";
import DynamicImports from "./components/DynamicImports";
import PhoneInput from "./components/PhoneInput";
import { inVP, debounce } from "./utils";
import CaseStudiesListing from "./ListingPages/CaseStudiesListing";
import SearchListing from "./ListingPages/SearchListing";
import ContactUsListing from "./ListingPages/ContactUsListing";

export default new (class App {
	constructor() {
		this.setDomMap();
		this.previousScroll = 0;

		// dom ready shorthand
		$(() => {
			this.domReady();
		});

		this.window.on("beforeunload", () => {
			gsap.to(this.siteLoader.find("post-loader"), {
				opacity: 1,
				duration: 0.3,
				ease: "power2.out",
			});
		});

		window.addEventListener(
			"pageshow",
			(event) => {
				if (event.persisted) {
					gsap.to(this.siteLoader.find("svg"), {
						autoAlpha: 0,
						duration: 1,
					});
				}
			},
			{ passive: true },
		);
	}

	siteLoaded = () => {
		this.htmlBody.addClass("site-loaded");

		this.siteLoader.addClass("hide");

		$(".banner-v1").addClass("active");

		// setTimeout(() => {
		new Banner();
		// }, 10==0);
		const animationObject = new Animations();

		if (!this.htmlBody.hasClass("visited")) {
			setTimeout(() => {
				this.cookiesPopup.addClass("active");
			}, 500);
		}
	};

	handleSplashScreen() {
		if (!this.htmlBody.hasClass("visited")) {
			// let load_time3 = 3;
			// let load_time = 5;
			// let zoom_level = 16;
			// let x = -400 + "px";
			// let y = -400 + "px";

			// let load_time1 = 2;
			// let x1 = -400 / 3 + "px";
			// let y1 = -400 / 3 + "px";
			// let zoom_level1 = zoom_level / 3;

			// let load_time2 = 1;
			// let x2 = -400 / 2 + "px";
			// let y2 = -400 / 2 + "px";
			// let zoom_level2 = zoom_level / 2;
			// if (this.window.width() >= 2500) {
			// 	load_time = 2;
			// 	zoom_level = 20;
			// 	x = -500 + "px";
			// 	y = -500 + "px";

			// 	x1 = this.window.width() / 3 + "px";
			// 	y1 = this.window.height() / 5 + "px";
			// 	zoom_level1 = 5;
			// 	load_time1 = 1;

			// 	x2 = this.window.width() / 12 + "px";
			// 	y2 = -150;
			// 	zoom_level2 = 12;
			// 	load_time2 = 3;
			// 	load_time = load_time1 + load_time2 + load_time3;
			// } else if (this.window.width() < 2500 && this.window.width() > 1900) {
			// 	load_time3 = 2;
			// 	zoom_level = 16;
			// 	x = -400 + "px";
			// 	y = -800 + "px";

			// 	x1 = this.window.width() / 3 + "px";
			// 	y1 = this.window.height() / 4 + "px";
			// 	zoom_level1 = 4;
			// 	load_time1 = 1;

			// 	x2 = this.window.width() / 6 + "px";
			// 	y2 = -100;
			// 	zoom_level2 = 8;
			// 	load_time2 = 2;
			// 	load_time = load_time1 + load_time2 + load_time3;
			// } else if (this.window.width() <= 1900 && this.window.width() > 1600) {
			// 	load_time3 = 2;
			// 	zoom_level = 14;
			// 	x = -400 + "px";
			// 	y = -400 + "px";

			// 	x1 = this.window.width() / 3.5 + "px";
			// 	y1 = this.window.height() / 5 + "px";
			// 	zoom_level1 = 4;
			// 	load_time1 = 1;

			// 	x2 = this.window.width() / 24.5 + "px";
			// 	y2 = -50;
			// 	zoom_level2 = 9;
			// 	load_time2 = 2;
			// 	load_time = load_time1 + load_time2 + load_time3;
			// } else if (this.window.width() <= 1600 && this.window.width() > 1279) {
			// 	load_time3 = 2;
			// 	zoom_level = 12;
			// 	x = -350 + "px";
			// 	y = -400 + "px";

			// 	x1 = 500 + "px";
			// 	y1 = 250 + "px";
			// 	zoom_level1 = 2;
			// 	load_time1 = 1;

			// 	x2 = 160;
			// 	y2 = 20;
			// 	zoom_level2 = 6;
			// 	load_time2 = 2;
			// 	load_time = load_time1 + load_time2 + load_time3;
			// } else if (this.window.width() <= 1279 && this.window.width() > 991) {
			// 	x = -250 + "px";
			// 	y = -250 + "px";
			// 	zoom_level = 12;
			// 	load_time3 = 1;

			// 	x1 = 350 + "px";
			// 	y1 = 230 + "px";
			// 	zoom_level1 = 1.5;
			// 	load_time1 = 1;

			// 	x2 = 150;
			// 	y2 = 100;
			// 	zoom_level2 = 4;
			// 	load_time2 = 1;
			// 	load_time = load_time1 + load_time2 + load_time3;
			// } else if (this.window.width() <= 991 && this.window.width() > 767) {
			// 	x = -300 + "px";
			// 	y = -100 + "px";
			// 	zoom_level = 10;
			// 	load_time3 = 0.5;

			// 	x1 = 250 + "px";
			// 	y1 = 420 + "px";
			// 	zoom_level1 = 1.5;
			// 	load_time1 = 0.5;

			// 	x2 = -100 + "px";
			// 	y2 = 100 + "px";
			// 	zoom_level2 = 6;
			// 	load_time2 = 1;
			// 	load_time = load_time1 + load_time2 + load_time3;
			// } else if (this.window.width() <= 767) {
			// 	x = -200 + "px";
			// 	y = -100 + "px";
			// 	zoom_level = 6;
			// 	load_time = 2;
			// }

			// let t1 = gsap.timeline({
			// 	delay: 2,
			// 	onComplete: () => {},
			// });
			// let t01 = gsap.timeline({
			// 	delay: 1,
			// 	onComplete: () => {
			// 		setTimeout(() => {
			// 			t02.play();
			// 		}, 30);
			// 	},
			// });

			// let t02 = gsap.timeline({
			// 	paused: true,
			// 	onComplete: () => {
			// 		setTimeout(() => {
			// 			t03.play();
			// 		}, 30);
			// 	},
			// });
			// let t03 = gsap.timeline({ paused: true });

			// let t3 = gsap.timeline({ delay: 1 });
			// let t4 = gsap.timeline({
			// 	delay: 1,
			// 	onComplete: () => {
			// 		gsap.to(this.siteLoader.find(".progress"), {
			// 			duration: 1,
			// 			yPercent: 10,
			// 			opacity: 0,
			// 			ease: "sine.out",
			// 		});

			// 		let temp = setTimeout(() => {
			// 			this.siteLoaded();
			// 			clearInterval(temp);
			// 		}, 10);
			// 	},
			// });

			// if (this.window.width() > 767) {
			// 	t01.to(this.banner.find("svg .shape"), {
			// 		scale: zoom_level1,
			// 		y: y1,
			// 		x: x1,
			// 		ease: "sine.out",
			// 		duration: load_time1,
			// 	});

			// 	t02.to(this.banner.find("svg .shape"), {
			// 		scale: zoom_level2,
			// 		y: y2,
			// 		x: x2,
			// 		ease: "sine.out",
			// 		duration: load_time2,
			// 	});

			// 	t03.to(this.banner.find("svg .shape"), {
			// 		scale: zoom_level,
			// 		y: y,
			// 		x: x,
			// 		ease: "sine.out",
			// 		duration: load_time3,
			// 	});
			// } else {
			// 	t01.to(this.banner.find("svg .shape"), {
			// 		scale: zoom_level,
			// 		y: y,
			// 		x: x,
			// 		ease: "sine.out",
			// 		duration: load_time,
			// 	});
			// }

			// t3.to(this.siteLoader.find(".progress-bar"), {
			// 	x: "0%",
			// 	duration: load_time - 1,
			// 	ease: "sine.inOut",
			// });

			// t4.to(this.siteLoader.find(".progress-bar .progress-counter .number"), {
			// 	duration: load_time,
			// 	innerHTML: 100,
			// 	roundProps: "innerHTML",
			// 	ease: "",
			// 	onUpdate: function () {
			// 		document.getElementById("percentage").textContent = Math.round(
			// 			this.targets()[0].innerHTML,
			// 		);
			// 	},
			// });


			// new changes 
			// this.siteLoader.find(".post-loader").css("display", "block");
			this.siteLoader.find(".logo-middle").css("display", "block");

			let t1 = gsap.timeline();
			let t2 = gsap.timeline({
				delay: 0.3,
				onComplete: () => {
					t20.play();
				},
			});

			let t20 = gsap.timeline({
				delay: 0.3,
				paused: true,
				onComplete: () => {
					t3.play();
				},
			});

			let t3 = gsap.timeline({
				delay: 0.3,
				paused: true,
				onComplete: () => {
					setTimeout(() => {
						this.siteLoaded();
					}, 10);
				},
			});

			t1.to(this.siteLoader.find(".logo-middle"), 0.1, {
				opacity: 1,
				ease: "power2.out",
			});

			t2.to(this.siteLoader.find(".logo-middle"), 1, {
				scale: 1,
				ease: "sine.out",
			});

			t20.to(this.siteLoader.find(".logo-middle"), 0.5, {
				opacity: 0,
				ease: "power2.out",
			});

			t3.to(this.siteLoader.find(".post-loader"), {
				clipPath: "polygon(100% 0, 100% 0, 100% 100%, 100% 100%)",
				duration: 1,
				ease: "sine.out",
			});
		} else {
			// this.siteLoader.find(".post-loader").css("display", "block");
			// this.siteLoader.find(".logo-middle").css("display", "none");

			

			let t1 = gsap.timeline({
				delay: 0.3,
				onComplete: () => {
					setTimeout(() => {
						this.siteLoaded();
					}, 10);
				},
			});

			t1.to(this.siteLoader.find(".post-loader"), {
				clipPath: "polygon(100% 0, 100% 0, 100% 100%, 100% 100%)",
				duration: 1,
				ease: "sine.out",
			});
		}
	}

	domReady = () => {
		this.handleUserAgent();
		this.handleSplashScreen();
		this.captchaLoad();
		this.handleUserAgent();
		this.windowResize();
		this.bindEvents();
		this.initComponents();
	};

	initComponents = () => {
		new Header({
			header: this.header,
			htmlBody: this.htmlBody,
		});

		if (this.mapContainer.length) {
			new Maps({
				mapContainer: this.mapContainer,
			});
		}

		new BlockMain();

		new DynamicImports();

		setTimeout(() => {
			new PhoneInput();
		}, 1000);

		if (this.caseStudiesListing.length) {
			new CaseStudiesListing();
		}
		if (this.searchListing.length) {
			new SearchListing();
		}
		if (this.contactListing.length) {
			new ContactUsListing();
		}

		if ($("#ccm-panel-dashboard").length) {
			$("#ccm-panel-dashboard").attr("data-lenis-prevent", "");
		}

		if ($('.form-group[data-formidable-type="phone"]').length) {
			$('.form-group[data-formidable-type="phone"]').attr(
				"data-lenis-prevent",
				"",
			);
		}

		if (
			!this.htmlBody.hasClass("edit-mode") &&
			this.htmlBody.hasClass("win-os") &&
			$(window).width() > 1279
		) {
			setTimeout(() => {
				lenis = new Lenis({
					lerp: 0.07,
				});
				function raf(time) {
					lenis.raf(time);
					requestAnimationFrame(raf);
				}
				requestAnimationFrame(raf);
			}, 1000);
		}

		// if (!this.htmlBody.hasClass("edit-mode") && $(window).width() > 1279) {
		// let rellax = new Rellax(".parallax-anim");
		// }
		if (this.htmlBody.hasClass("win-os")) {
			let popUp = this.html.find(".vacancy-form-popUp");

			let openPopUpBtn = this.html.find(".applyBtn");
			let closePopUpBtn = $(popUp).find(".close");
			$(openPopUpBtn).on("click", function () {
				var idVal = $(this).attr("id");
				var popUpID = idVal.split("_")[1];

				$(`#popUp-${popUpID}`).addClass("active");
				$("body").addClass("overflow-hidden");
				lenis.stop();
				console.log("Pop-up button clicked. Pop-up ID:", popUpID);
				// if (this.html.hasClass("lenis")) {
				// }
			});

			$(".vacancy-form-popUp .close").on("click", function () {
				$(popUp).removeClass("active");
				$("body").removeClass("overflow-hidden");
				lenis.start();
			});
		}
		if (this.bannerSection.length) {
			$("#scroll-to-banner").on("click", function (event) {
				event.preventDefault();
				var $section = $($(this).attr("href"));
				$("html, body").animate(
					{
						scrollTop: $section.offset().top - 100,
					},
					2000,
				);
			});
		}

		if (!this.wrapper.hasClass("page-template-home-Page")) {
			this.header.addClass("dark-header");
		}

		if($('.login-page').length) {
			this.header.removeClass("dark-header");
		}

		let timeoutId = "";
		let isHovered = false;
		this.arrowedBtn.each((i, el) => {
			$(el).on(
				"mouseenter",
				debounce(() => {
					clearTimeout(timeoutId);

					timeoutId = setTimeout(() => {
						isHovered = true;
						$(el).addClass("hovered");
					}, 50);
				}, 50),
			);

			$(el).on(
				"mouseleave",
				debounce(() => {
					clearTimeout(timeoutId);
					isHovered = false;
					$(el).removeClass("hovered");
				}, 200),
			);
		});

		this.cookiesPopup.find("a").on("click", (e) => {
			e.preventDefault();
			this.cookiesPopup.removeClass("active");
		});


	
	};

	captchaLoad = () => {
		$(window).on("scroll load", () => {
			if (inVP(this.formidable) && !this.formidable.hasClass("formInview")) {
				this.formidable.addClass("formInview");
			}
		});
	};

	setDomMap = () => {
		this.window = $(window);
		this.htmlNbody = $("body, html");
		this.html = $("html");
		this.htmlBody = $("body");
		this.siteLoader = $(".site-loader");
		this.header = $("header");
		this.siteBody = $(".site-body");
		this.bannerSection = $("#banner");
		this.banner = $(".banner-v1");
		this.footer = $("footer");
		this.gotoTop = $("#gotoTop");
		this.gRecaptcha = $(".g-recaptcha");
		this.wrapper = $(".wrapper");
		this.pushDiv = $(".push");
		this.mapContainer = $("#map_canvas");
		this.formidable = $(".formidable");
		this.inputs = $("input, textarea").not('[type="checkbox"], [type="radio"]');
		this.caseStudiesListing = $(".case-studies-main-container");
		this.searchListing = $(".search-main-container");
		this.contactListing = $(".contact-us-main-container");
		this.arrowedBtn = $(".channeline-btn");
		this.techBlock = $(".technology");
		this.cookiesPopup = $(".cookies-popUp");
	};

	bindEvents = () => {
		// Window Events

		this.window.resize(this.windowResize).scroll(this.windowScroll);

		// General Events
		const $container = this.wrapper;

		$container.on("click", ".disabled", () => false);

		// Specific Events
		this.gotoTop.on("click", () => {
			this.htmlNbody.animate({
				scrollTop: 0,
			});
		});

		this.inputs
			.on({
				focus: (e) => {
					const self = $(e.currentTarget);
					self.closest(".element").addClass("active");
				},
				blur: (e) => {
					const self = $(e.currentTarget);
					if (self.val() !== "") {
						self.closest(".form-group").addClass("active");
					} else {
						self.closest(".form-group").removeClass("active");
					}
				},
			})
			.trigger("blur");
	};

	windowResize = () => {
		this.screenWidth = this.window.width();
		this.screenHeight = this.window.height();

		console.log(this.pushDiv);
		// calculate footer height and assign it to wrapper and push/footer div
		if (this.pushDiv.length) {
			this.footerHeight = this.footer.outerHeight();
			this.wrapper.css("margin-bottom", -this.footerHeight);
			this.pushDiv.height(this.footerHeight);
		}
	};

	windowScroll = () => {
		const topOffset = this.window.scrollTop();

		this.header.toggleClass("top", topOffset > 80);
		this.header.toggleClass("sticky-header", topOffset > 300);
		if (topOffset > this.previousScroll || topOffset < 400) {
			this.header.removeClass("sticky-header");
		} else if (topOffset < this.previousScroll) {
			this.header.addClass("sticky-header");
			// Additional checking so the header will not flicker
			if (topOffset > 250) {
				this.header.addClass("sticky-header");
			} else {
				this.header.removeClass("sticky-header");
			}
		}

		this.previousScroll = topOffset;
		this.gotoTop.toggleClass(
			"active",
			this.window.scrollTop() > this.screenHeight / 2,
		);
	};

	handleUserAgent = () => {
		// detect mobile platform
		if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
			this.htmlBody.addClass("ios-device");
		}
		if (navigator.userAgent.match(/Android/i)) {
			this.htmlBody.addClass("android-device");
		}

		// detect desktop platform
		if (navigator.appVersion.indexOf("Win") !== -1) {
			this.htmlBody.addClass("win-os");
		}
		if (navigator.appVersion.indexOf("Mac") !== -1) {
			this.htmlBody.addClass("mac-os");
		}

		// detect IE 10 and 11P
		if (
			navigator.userAgent.indexOf("MSIE") !== -1 ||
			navigator.appVersion.indexOf("Trident/") > 0
		) {
			this.html.addClass("ie10");
		}

		// detect IE Edge
		if (/Edge\/\d./i.test(navigator.userAgent)) {
			this.html.addClass("ieEdge");
		}

		// Specifically for IE8 (for replacing svg with png images)
		if (this.html.hasClass("ie8")) {
			const imgPath = "/themes/theedge/images/";
			$("header .logo a img,.loading-screen img").attr(
				"src",
				`${imgPath}logo.png`,
			);
		}

		// show ie overlay popup for incompatible browser
		if (this.html.hasClass("ie9")) {
			const message = $(
				'<div class="no-support"> You are using outdated browser. Please <a href="https://browsehappy.com/" target="_blank">update</a> your browser or <a href="https://browsehappy.com/" target="_blank">install</a> modern browser like Google Chrome or Firefox.<div>',
			);
			this.htmlBody.prepend(message);
		}
	};
})();
