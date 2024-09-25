import intlTelInput from "intl-tel-input";
import { isInViewport } from "../utils";
import "intl-tel-input/build/css/intlTelInput.css";

export default class PhoneInput {
	constructor() {
		this.window = $(window);
		this.$phoneInputs = $('input[type="tel"]');
		this.contactSection = $("contact-form_section");
		if (!this.$phoneInputs.length) {
			return;
		}
		this.globalIti = null; // Define globalIti at the class level
		this.init();

		// this.window.on("scroll", ()=> {
		// this.scrollEvents();
		// });
		if (this.window.width() > 1279) {
			this.wrapCountryList();
		}
	}

	init = () => {
		this.$phoneInputs.each((i, el) => {
			this.globalIti = intlTelInput(el, {
				initialCountry: "us",
				nationalMode: false,
				autoHideDialCode: false,
				separateDialCode: true, // Separate dial code and number
				preferredCountries: ["ae"],
				utilsScript:
					"https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.11/js/utils.min.js",
			});

			const data = this.globalIti.getSelectedCountryData();
			$("input[type='tel']")
				.parents(".form-group")
				.find("#" + $(el)[0].id + "-dial-code")
				.val("+" + data.dialCode);

			$("input[type='tel']").on("countrychange", (e) => {
				const code = this.globalIti.getSelectedCountryData().dialCode;
				$(e.currentTarget)
					.parents(".form-group")
					.find("#" + $(el)[0].id + "-dial-code")
					.val("+" + code);
			});

			$(".iti__flag-container").on("click", function () {
				$(this).toggleClass("active");
			});
		});

		this.$phoneInputs.on("input", function (e) {
			const input = e.target;
			let value = input.value.replace(/\D/g, ""); // Remove non-numeric characters

			// Limit the input to 15 characters
			if (value.length > 15) {
				value = value.slice(0, 15);
			}

			$(input).val(value);
			$("#" + $(input)[0].id + "-number-original").val(value);
		});
	};

	// scrollEvents = () => {
	// 	// console.log($('.iti__country-list'));
	//    if( !$('.iti__country-list').hasClass('iti__hide')) {
	// 	console.log('hdh')
	// 	$('.iti__country-list').addClass('iti__hide');
	// 	// $('.iti__selected-flag').removeAttr('aria-activedescendant');
	// 	$('.iti__selected-flag').attr('aria-expanded', false);
	//    }
	// }

	wrapCountryList = () => {
		$(".iti__country-list li").wrapAll(
			'<ul class="iti__country-list-wrap"></ul>',
		);
	};
}
