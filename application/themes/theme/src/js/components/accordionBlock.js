import gsap from "gsap";
import { SplitText } from "gsap/SplitText";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(SplitText, ScrollTrigger);



export default class accordionBlock {
	constructor() {
		
		this.init();
	}

	init = () => {
		console.log('init');
	    this.setDomMap();
		this.faq();

		let timeout = 2000;
		if ($(this.body).hasClass("visited")) {
			timeout = 3200;
		}
		setTimeout(() => {
			this.animations();
		}, timeout);
	};

	setDomMap = () => {
		this.window =  $(window);
		this.body = $("body");
		this.accordionBlock = $('.accordion-block');
	};

	faq = () => {
		$(".acc-container .acc:nth-child(1) .acc-head").addClass("active");
		$(".acc-container .acc:nth-child(1) .acc-content").slideDown();
		$(".acc-container .acc:nth-child(1)").addClass("active");
		// if ($(".faq-listing").length) {
		// 	$(".acc-container:nth-child(1) .acc .acc-head").removeClass("active");
		// 	$(".acc-container:nth-child(1) .acc .acc-content").slideUp();

		// 	$(".acc-container:nth-child(2) .acc:nth-child(1) .acc-head").addClass(
		// 		"active",
		// 	);
		// 	$(
		// 		".acc-container:nth-child(2) .acc:nth-child(2) .acc-content",
		// 	).slideDown();
		// }

		$(".acc-head").on("click", function () {
			if ($(this).hasClass("active")) {
				$(this).siblings(".acc-content").slideUp(500);
				$(this).removeClass("active");
				$(this).closest(".acc").removeClass("active");
			} else {
				$(".acc-content").slideUp(500);
				$(".acc-head").removeClass("active");
				$(this).siblings(".acc-content").slideToggle(500);
				$(this).toggleClass("active");

				$(".acc").removeClass("active"); // Remove class from all .acc elements
				$(this).closest(".acc").addClass("active"); // Add class to closest .acc
			}
		});
	};


	animations = () => {
      if(this.window.width() > 991) {
		let accordion = this.accordionBlock.find('.acc');

	   
		let t1 = gsap.timeline({});

		t1.to($(accordion), {
			x: "0px",
			opacity: 1,
			duration: 0.8,
			stagger: 0.3,
			ease: 'power2.out'
		});

		ScrollTrigger.create({
			trigger: this.accordionBlock.find('.acc-container'),
			start: "top 80%",
			animation: t1
		});
	  } else {
		let container = this.accordionBlock.find('.acc-container');

		let t1 = gsap.timeline({});

		t1.to($(container), {
	        y: "0px",
			opacity: 1,
			duration: 0.8,
			ease: 'power2.out'
		});

		ScrollTrigger.create({
			trigger: this.accordionBlock.find('.acc-container'),
			start: "top 90%",
			animation: t1
		});
	  }
	};
}
