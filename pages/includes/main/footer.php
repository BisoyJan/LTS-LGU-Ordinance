<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle sidebar on mobile
    document.querySelector('.toggle-btn').addEventListener('click', function () {
        document.querySelector('.sidebar').classList.toggle('active');
    });

    // Set active link
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function () {
            document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (function () {
        'use strict';
        window.addEventListener('load', function () {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.getElementsByClassName('needs-validation');
            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function (form) {
                form.addEventListener('submit', function (event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();

    function showToast(message, type) {
        let toastElement = document.getElementById("toastMessage");
        let toastBody = document.getElementById("toastBody");

        // Update toast content and color
        toastBody.innerHTML = message;
        toastElement.classList.remove("bg-success", "bg-danger", "bg-warning");
        toastElement.classList.add("bg-" + type);

        // Show the toast
        let toast = new bootstrap.Toast(toastElement);
        toast.show();
    }
</script>
</body>

</html>
