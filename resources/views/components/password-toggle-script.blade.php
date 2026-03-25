<style>
    /* Hide default password toggle in Edge/IE */
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear {
        display: none;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.body.addEventListener('click', function (e) {
            const toggle = e.target.closest('.password-toggle');
            if (toggle) {
                const container = toggle.closest('.relative');
                if (!container) return;

                const input = container.querySelector('input');
                const eyeIcon = toggle.querySelector('.eye-icon');
                const eyeSlashIcon = toggle.querySelector('.eye-slash-icon');

                if (input && eyeIcon && eyeSlashIcon) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        eyeIcon.classList.add('hidden');
                        eyeSlashIcon.classList.remove('hidden');
                    } else {
                        input.type = 'password';
                        eyeIcon.classList.remove('hidden');
                        eyeSlashIcon.classList.add('hidden');
                    }
                }
            }
        });
    });
</script>