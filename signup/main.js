

let popup = document.querySelector('.popup')

let captchaCode

let url = window.location.href

let invitation = (url.search('invite=') > -1) ? window.location.href.split('=')[1] : 0
console.log(invitation)

$('.captcha-form').submit(e => {
    captchaCode = $('.captcha').val()
    e.preventDefault()

    $('.msg-captcha').load('./signup.php', {
        uid: $('.uid').val(),
        email: $('.email').val(),
        pwd: $('.pwd').val(),
        pwdCheck: $('.pwdCheck').val(),
        invation: invitation,
        captcha: captchaCode

    }, (data, status) => {
        if (status == 'success')
            if (document.querySelector('.msg').innerText.trim().length == 0)
                document.querySelector('.msg').style.display = 'none'
            
            else if (error == 7) {
                popup.textContent = '';
                let elt = document.createElement('div')
                elt.classList.add('captcha-form')
                let successMsg = document.createElement('div')
                successMsg.classList.add('successMsg')
                successMsg.textContent = errorMsg

                elt.appendChild(successMsg)

                let p = document.createElement('p')
                p.classList.add('redirect-warning')
                p.innerHTML = 'You will be redirected to <a href="/login" class="real-link">Login</a> page in:'

                elt.appendChild(p)
                let countDownNum = 8
                let countDown = document.createElement('div')
                countDown.classList.add('countDown')
                countDown.textContent = countDownNum
                
                elt.appendChild(countDown)
                popup.appendChild(elt)
                
                window.setInterval(() => {
                    if (countDownNum > 0) {
                        countDownNum -= 1
                        countDown.textContent = countDownNum
                    }else {
                        window.location.href = '/login'
                    }

                }, 1000)
            }
            
        if (error == 5 || error == 6) {
            //Show popup
            popup.style.display = 'flex'
        }else {
            
            if (error != 7) {
                popup.style.display = 'none'
                document.querySelector('.msg').textContent = errorMsg
                document.querySelector('.msg-captcha').textContent = ''
                document.querySelector('.msg').style.display = ''
                console.log(errorMsg)
            }

        }
    })



})

$('.signup-form').submit(e => {
    e.preventDefault()
    $('.msg').load('./signup.php', {
        uid: $('.uid').val(),
        email: $('.email').val(),
        pwd: $('.pwd').val(),
        pwdCheck: $('.pwdCheck').val(),
        invation: invitation,
        captcha: captchaCode

    }, (data, status) => {
        if (status == 'success')
            if (document.querySelector('.msg').innerText.trim().length == 0)
                document.querySelector('.msg').style.display = 'none'
        if (error == 6) {
            //Show popup
            popup.style.display = 'flex'
            document.querySelector('.msg').style.display = 'none'
            document.querySelector('.msg-captcha').textContent = errorMsg
        } else if (error == 5) {
            popup.style.display = 'flex'
            document.querySelector('.msg').style.display = 'none'
        }else if (error == 7) {
            popup.textContent = '';
            let elt = document.createElement('div')
            elt.classList.add('captcha-form')
            let successMsg = document.createElement('div')
            successMsg.classList.add('successMsg')
            successMsg.textContent = errorMsg

            elt.appendChild(successMsg)

            let p = document.createElement('p')
            p.classList.add('redirect-warning')
            p.innerHTML = 'You will be redirected to <a href="/login" class="real-link">Login</a> page in:'

            elt.appendChild(p)
            let countDownNum = 8
            let countDown = document.createElement('div')
            countDown.classList.add('countDown')
            countDown.textContent = countDownNum

            elt.appendChild(countDown)
            popup.appendChild(elt)

            window.setInterval(() => {
                if (countDownNum > 0) {
                    countDownNum -= 1
                    countDown.textContent = countDownNum
                } else {
                    window.location.href = '/login'
                }

            }, 1000)
        }
    })
})