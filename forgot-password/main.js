



let uid = document.querySelector('.uid')
$('form').submit(event => {
    event.preventDefault()
    $('.msg').load('./requestNewPassword.php', {
        email: uid.value,
    },
        (data, success) => {
            console.log(success)
        }
    )
})