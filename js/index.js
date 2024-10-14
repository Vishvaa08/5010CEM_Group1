let splashScreen = document.querySelector(".splash-screen");
let splash = document.querySelectorAll(".splash");

window.addEventListener('DOMContentLoaded', ()=>{

    setTimeout(() => {
        
        splash.forEach((span, idx)=>{
            setTimeout(()=>{
                span.classList.add('active');
            }, (idx + 1) * 500)
        });

        setTimeout(()=>{
            splash.forEach((span, idx)=>{
                setTimeout(()=>{
                    span.classList.remove('active');
                    span.classList.add('fade');
                }, (idx + 1) * 50)
            })
        }, 2000);

        setTimeout(()=>{
            splashScreen.style.top = '-100vh';
        }, 2300)

    })
})