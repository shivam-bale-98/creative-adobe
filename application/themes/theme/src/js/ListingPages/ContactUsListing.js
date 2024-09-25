import Locations from '../components/Maps/types/locations';

export default class ContactUsListing {
    constructor() {
        this.init();
        this.bindEvents();
    }

    init = () => {
        this.page = 1;
        this.mainContainer = $(".contact-us-main-container");
        this.token = this.mainContainer.find('input[name="ccm_token"]');
        this.itemList = this.mainContainer.find(".contact-us--listing");
        this.errorMessage = this.mainContainer.find(".error-message");
        this.filterOptions = this.mainContainer.find("#location");
        this.keywords = this.mainContainer.find("#keywords");
        this.location = this.filterOptions.find(":selected").val();
        this.loader = this.mainContainer.find(".loader");
        this.initializeMap();
    };

    bindEvents = () => {
        this.filterOptions.on("change", this.filtersChanged);
        this.keywords.on("keyup", this.delayFilter);
    };

    filtersChanged = (e) => {
        let input = e.currentTarget ? e.currentTarget : e;
        this.location = $(input).data("value") || $(input).val();
        this.resetPage();
        this.filterSearch();
    };

    delayFilter = () => {
        this.resetPage();
        clearInterval(this.timer);
        this.timer = setTimeout(() => {
            this.filterSearch();
        }, 1000);
    };

    resetPage = () => {
        this.page = 1;
    };

    clearContainer = () => {
        this.itemList.empty();
    };

    nextPage = () => {
        clearTimeout(this.timer);
        this.timer = setTimeout(this.loadMore, 500);
    };

    hideLoader = () => {
        this.loader.hide();
    };

    showLoader = () => {
        this.loader.show();
    };

    filterSearch = () => {
        this.resetPage();
        this.showLoader();
        this.clearContainer();
        $.ajax({
            dataType: "html",
            type: "GET",
            data: this.formFilters(),
            url: this.getCurrentPagePath(),
            success: this.applyFilters,
        });
    };

    applyFilters = (response) => {
        this.hideLoader();
        response = JSON.parse(response);
        let data;
        if (response.success) {
            data = $(response.data);
            this.itemList.empty();
            this.itemList.append(data);
        }
        this.checkErrorMessage(response.data);
        this.addPushState();

        setTimeout(()=> {
            console.log('apply filters')
            this.initializeMap();
        }, 500);
    };

    checkErrorMessage = (value) => {
        value === "" ? this.errorMessage.show() : this.errorMessage.hide();

        if(value === "") {
            //show error
            this.mainContainer.find('.contact-us--listing').addClass('height-auto');
        } else {
            this.mainContainer.find('.contact-us--listing').removeClass('height-auto');
        }
    };

    addPushState = () => {
        if (window.history.pushState) {
            window.history.pushState(
                "",
                "",
                "?keywords=" + this.getKeywords() + "&location=" + this.getLocation()
            );
        }
    };

    getCurrentPagePath = () => {
        return location.origin + location.pathname;
    };

    addPage = () => {
        this.page += 1;
    };

    initializeMap = () => {
        // console.log('locations');
        setTimeout(()=> {
            this.map = new Locations();
        }, 1000)
        
    };

    formFilters = () => {
        return {
            page: this.page,
            keywords: this.getKeywords(),
            location: this.getLocation(),
            ccm_token: this.getToken(),
        };
    };

    getKeywords = () => {
        return this.keywords.val() ? encodeURIComponent(this.keywords.val()) : "";
    };

    getLocation = () => {
        return this.location ? encodeURIComponent(this.location) : "";
    };

    getToken = () => {
        return this.token ? this.token.val() : "";
    };

}
