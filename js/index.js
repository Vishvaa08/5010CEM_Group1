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

const buttons = document.querySelectorAll('.info-btn');
        buttons.forEach(button => {
            button.addEventListener('click', function() {
                const infoId = this.getAttribute('data-id');
                const infoDetails = document.getElementById(infoId);

                if (infoDetails.style.display === 'block') {
                    infoDetails.style.display = 'none';
                } else {
                    document.querySelectorAll('.info-details').forEach(detail => {
                        detail.style.display = 'none';
                    });
                    infoDetails.style.display = 'block';
                }
            });
        });

        const modal = document.getElementById("contactModal");
        const openModal = document.getElementById("openModal");
        const closeBtn = document.querySelector(".close");
        
        openModal.addEventListener("click", function(event) {
            event.preventDefault();
            modal.style.display = "flex"; 
            document.body.style.overflow = 'hidden'; 
        });
        
        closeBtn.addEventListener("click", function() {
            modal.style.display = "none"; 
            document.body.style.overflow = ''; 
        });
        
        window.addEventListener("click", function(event) {
            if (event.target === modal) {
                modal.style.display = "none"; 
                document.body.style.overflow = ''; 
            }
        });
        
        

        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('contactForm');
            
            form.addEventListener('submit', function (event) {
                event.preventDefault(); 
                
                const formData = new FormData(form);
                
                fetch('submit_contact.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(result => {
                    showToast('Message successfully sent!');
                    form.reset();  
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
        
        function showToast(message) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.classList.add('show');
        
            setTimeout(function () {
                toast.classList.remove('show');
            }, 3000);
        }