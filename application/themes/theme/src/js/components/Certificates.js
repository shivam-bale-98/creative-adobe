import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";


gsap.registerPlugin(ScrollTrigger);

export default class Certificates {
    constructor() {
        this.init();
    }

    init = () => {
        this.setDomMap();
        this.bindEvents();
    };

    setDomMap = () => {
        this.window = $(window);
        this.body = $("body");
        this.certificateCard = $('.certificate-card');
    }

    bindEvents = () => {
        this.certificateCard.each((index, el)=> {
            let t1 = gsap.timeline({});


            t1.to($(el), {
                y: "0px",
                opacity: 1,
                duration: 1,
                ease: 'sine.out', 
                delay : 0.1 * index 
            });

            ScrollTrigger.create({
                trigger: $(el),
                start: 'top 90%',
                animation: t1
            })
        });
    }
}