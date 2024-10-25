window.addEventListener('load', () => {
    document.getElementById('btnCreate').addEventListener('click', validate);
});

function validate(btn) {
    const isValid = Array.from(document.querySelectorAll("input[id^='txt']"))
        .reduce((p, c) => {
            const isValid = !!c.value;

            c.classList[isValid ? 'remove' : 'add']('invalid');

            return p && isValid;
        }, true);

    if (!isValid) btn.preventDefault();
}