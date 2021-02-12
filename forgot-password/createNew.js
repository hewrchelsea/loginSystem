let msg = document.querySelector('.msg')


if (msg) {

    let selector = document.querySelector('.selector')
    let validator = document.querySelector('.validator')
    let pwd = document.querySelector('.pwd')
    let pwdConfirm = document.querySelector('.pwdConfirm')


    $('form').submit(event => {
        event.preventDefault()
        $('.msg').load('./resetPwd.php', {
            selector: selector.value,
            validator: validator.value,
            pwd: pwd.value,
            pwdConfirm: pwdConfirm.value
        },
            (data, success) => {
                console.log(success)
            }
        )
    })
}