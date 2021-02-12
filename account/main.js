


const btn = document.querySelector('.shareBtn');


let url = document.querySelector('.a-parent a').href


if (btn) {


    btn.onclick = async (filesArray) => {
        if (navigator.canShare) {
            navigator.share({
                title: 'This is the title',
                text: 'This is the description',
                url: 'https://www.example.com',
            })
        }else {
            //Cannot share
            //Show the links
            btn.style.display = 'none'
            document.querySelector('.links').style.display = 'flex'
        }
    }
    
    
    let clipboardBtn = document.querySelector('.clipboard')
    
    clipboardBtn.onclick = e => {
        navigator.clipboard.writeText(url).then(function () {
            /* clipboard successfully set */
        }, function () {
            /* clipboard write failed */
            console.log('Failed to copy text')
        });
    
    }

}
