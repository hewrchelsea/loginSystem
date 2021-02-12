

let form = document.querySelector('form')
let msg = document.querySelector('.msg')

let code = document.querySelector('.code')
if (form && msg) {


    $('form').submit(event => {
        event.preventDefault()
        $('.msg').load('./confirmEmail.php', {
            code: code.value,
        },
        (data, success) => {
                console.log(success)
        }
        )
    })

}