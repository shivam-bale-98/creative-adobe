import ListingBlock from "./ListingBlock";
import downloadsBlock from "./DownloadsBlock";
import ourPeopleBlock from './OurPeopleBlock';
export default class BlockMain {
    constructor() {
        this.setDomMap();
    };

    setDomMap = () => {
        this.listingBlock = $(".listing--block");
        this.downloadsBlock = $(".downloads--listing");
        this.ourPeopleBlock = $(".team-listing");
        // dom ready shorthand
        $(() => {
            this.domReady();
        });
    };

    domReady = () => {
        this.initComponents();
    };

    initComponents = () => {
        if(this.listingBlock.length) {
            new ListingBlock();
        }
        if(this.downloadsBlock.length) {
            new downloadsBlock();
        }
        if(this.ourPeopleBlock.length) {
            new ourPeopleBlock();
        }
    }
}