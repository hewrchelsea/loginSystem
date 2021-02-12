



let uid = document.querySelector('.uid')
let pwd = document.querySelector('.pwd')
$('form').submit(event => {
    event.preventDefault()
    $('.msg').load('./login.php', {
        uid: uid.value,
        pwd: pwd.value
    },
    (data, success) => {
        console.log(success)
    }
    )
})