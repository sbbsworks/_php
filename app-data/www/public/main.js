try {
    [...document.querySelectorAll('form')].map((form) => {
        let submitted = false;
        form.addEventListener('submit', (ev) => {
            if(submitted) {
                ev.preventDefault();
            }
            submitted = true;
        });
    });
    document.querySelector('select[name="updated_user"]')?.addEventListener('change', (ev) => {
        if(document.querySelector('input[name="user_name"]')) {
            const selectedOption = document.querySelector(`option[value="${ev.currentTarget.value}"]`);
            document.querySelector('input[name="user_name"]').value = selectedOption?.dataset.name || '';
            document.querySelector('input[name="user_email"]').value = selectedOption?.dataset.email || '';
            document.querySelector('input[name="user_telegram"]').value = selectedOption?.dataset.telegram || '';
        }
    });
    document.querySelector('select[name="notified_user"]')?.addEventListener('change', (ev) => {
        if(document.querySelector('input[name="user_name"]')) {
            const selectedOption = document.querySelector(`option[value="${ev.currentTarget.value}"]`);
            document.querySelector('input[name="user_name"]').value = selectedOption?.dataset.name || '';
            document.querySelector('input[name="user_email"]').value = selectedOption?.dataset.email || '';
            document.querySelector('input[name="user_telegram"]').value = selectedOption?.dataset.telegram || '';
        }
    });
} catch(error) {
    console.error(error.message);
}