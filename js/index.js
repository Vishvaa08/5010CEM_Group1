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

            document.querySelectorAll('.info-btn').forEach(button => {
            button.addEventListener('click', function () {
                const details = document.querySelector(`#${this.dataset.id}`);
                details.classList.toggle('open');
                
                const downArrow = this.querySelector('.arrow.down');
                const upArrow = this.querySelector('.arrow.up');
                
                if (details.classList.contains('open')) {
                    downArrow.style.display = 'none';
                    upArrow.style.display = 'inline';
                } else {
                    downArrow.style.display = 'inline';
                    upArrow.style.display = 'none';
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
                
                fetch('/php_functions/submit_contact.php', {
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