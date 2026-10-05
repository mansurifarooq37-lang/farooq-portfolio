const text = [
    "Full Stack Developer",
    "PHP Developer",
    "Laravel Developer",
    "Web Designer"
];

let count = 0;
let index = 0;
let currentText = "";
let letter = "";

(function type() {

    if (count === text.length) {
        count = 0;
    }

    currentText = text[count];
    letter = currentText.slice(0, ++index);

    document.querySelector(".typing").textContent = letter;

    if (letter.length === currentText.length) {

        setTimeout(() => {

            index = 0;
            count++;
            type();

        }, 1500);

    } else {

        setTimeout(type, 100);

    }

})();
/* =========================================
   ANIMATED PARTICLE NETWORK
========================================= */

const canvas = document.getElementById("particles-canvas");
const ctx = canvas.getContext("2d");

let particles = [];
let particleCount = window.innerWidth < 768 ? 35 : 65;

function resizeCanvas(){

    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

}

resizeCanvas();

window.addEventListener("resize", () => {

    resizeCanvas();

    particleCount = window.innerWidth < 768 ? 35 : 65;

    createParticles();

});


function createParticles(){

    particles = [];

    for(let i = 0; i < particleCount; i++){

        particles.push({

            x:Math.random() * canvas.width,

            y:Math.random() * canvas.height,

            size:Math.random() * 2.5 + 1,

            speedX:(Math.random() - 0.5) * 0.35,

            speedY:(Math.random() - 0.5) * 0.35

        });

    }

}


function drawParticles(){

    ctx.clearRect(
        0,
        0,
        canvas.width,
        canvas.height
    );


    /* Draw particles */

    particles.forEach(p => {

        ctx.beginPath();

        ctx.arc(
            p.x,
            p.y,
            p.size,
            0,
            Math.PI * 2
        );

        ctx.fillStyle = "rgba(108,99,255,0.45)";

        ctx.fill();

    });


    /* Draw connecting lines */

    for(let i = 0; i < particles.length; i++){

        for(let j = i + 1; j < particles.length; j++){

            const dx = particles[i].x - particles[j].x;

            const dy = particles[i].y - particles[j].y;

            const distance = Math.sqrt(
                dx * dx + dy * dy
            );


            if(distance < 130){

                const opacity =
                    0.12 * (1 - distance / 130);


                ctx.beginPath();

                ctx.moveTo(
                    particles[i].x,
                    particles[i].y
                );

                ctx.lineTo(
                    particles[j].x,
                    particles[j].y
                );

                ctx.strokeStyle =
                    `rgba(108,99,255,${opacity})`;

                ctx.lineWidth = 1;

                ctx.stroke();

            }

        }

    }


    /* Move particles */

    particles.forEach(p => {

        p.x += p.speedX;
        p.y += p.speedY;


        /* Screen wrapping */

        if(p.x < 0) p.x = canvas.width;

        if(p.x > canvas.width) p.x = 0;

        if(p.y < 0) p.y = canvas.height;

        if(p.y > canvas.height) p.y = 0;

    });


    requestAnimationFrame(drawParticles);

}


createParticles();

drawParticles();
/* =========================================
   SCROLL PARALLAX BACKGROUND
========================================= */

const parallaxGlows = document.querySelectorAll(".glow");
const parallaxSymbols = document.querySelectorAll(".code-symbol");

window.addEventListener("scroll", function(){

    const scrollY = window.scrollY;


    /* Glows move slowly */

    parallaxGlows.forEach((glow, index) => {

        const speed = (index + 1) * 0.08;

        glow.style.transform =
            `translateY(${scrollY * speed}px)`;

    });


    /* Code symbols move at different speeds */

    parallaxSymbols.forEach((symbol, index) => {

        const speed = 0.03 + (index * 0.01);

        const direction =
            index % 2 === 0 ? 1 : -1;

        symbol.style.transform =
            `translateY(${scrollY * speed * direction}px)`;

    });

});
/* =========================================
   SCROLL REVEAL
========================================= */

const revealElements = document.querySelectorAll(
    ".reveal, .reveal-left, .reveal-right, .reveal-card"
);

const revealObserver = new IntersectionObserver(
    function(entries, observer){

        entries.forEach(function(entry){

            if(entry.isIntersecting){

                entry.target.classList.add("show");

                observer.unobserve(entry.target);

            }

        });

    },
    {
        threshold:0.15
    }
);


revealElements.forEach(function(element){

    revealObserver.observe(element);

});
/* ================================
   STEP 8 - NAVBAR SCROLL + ACTIVE
================================ */

const header = document.querySelector(".header");
const navLinks = document.querySelectorAll(".nav-links a");

const sections = document.querySelectorAll(
    "#home, #about, #skills, #projects, #contact"
);

window.addEventListener("scroll", function(){

    /* Navbar glass effect */
    if(window.scrollY > 40){
        header.classList.add("scrolled");
    }else{
        header.classList.remove("scrolled");
    }

    /* Active section */
    let currentSection = "";

    sections.forEach(function(section){

        const sectionTop = section.offsetTop - 140;
        const sectionHeight = section.offsetHeight;

        if(
            window.scrollY >= sectionTop &&
            window.scrollY < sectionTop + sectionHeight
        ){
            currentSection = section.getAttribute("id");
        }

    });

    navLinks.forEach(function(link){

        link.classList.remove("active");

        const linkTarget = link.getAttribute("href");

        if(linkTarget === "#" + currentSection){
            link.classList.add("active");
        }

    });

});