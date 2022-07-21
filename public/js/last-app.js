//  форма
const $formJs = document.querySelector('.form-js'),
    $dropDwn = document.querySelectorAll('.drop-dwn');

$dropDwn.forEach(item => {
    item.addEventListener('click', e => {
        e.preventDefault();

        const $titleDrop = e.target.closest('.drop-dwn-title');
        const $titleDropText = item.querySelector('.inf');
        const $titleContent = e.target.closest('.drop-dwn-content');
        const $contentItem = e.target.closest('.con-item p');

        if ($titleDrop) {
            item.classList.toggle('active');
        }

        if ($titleContent) {
            $titleDropText.textContent = $contentItem.textContent;
            item.classList.remove('active');
        }

    });
})

// шапка язык
const $langMJs = document.querySelector('.lang-m-js');

$langMJs.addEventListener('click', e => {
    e.preventDefault();
    $langMJs.classList.toggle('active');
})


// квиз
const $sectionJs = document.querySelector('.section-js'),
    $btn_send_phone = document.querySelectorAll('#btn-send-phone'),
    $phone = $('#phone'),
    $go_first_step = $('.go-first-step'),
    $send_sms_again = $('#send-sms-again'),

    $sectionTypeAds = document.querySelector('.section-type-ads'),
    $blockQuizJs = document.querySelector('.block-quiz-js'),
    $phoneJs = document.querySelector('.phone-js'),
    $pinJs = document.querySelector('.pin-js'),
    $harkBack = document.querySelectorAll('.hark-back-js'),
    $bNums = document.querySelector('.b-nums'),
    $hidInput = document.querySelector('.hid-input'),
    $boxTypeItem = document.querySelectorAll('.box-type-item'),
    $sectionFill = document.querySelector('.section-fill'),
    $sectionSuccessfully = document.querySelector('.section-successfully'),
    $btnStartGoJs = document.querySelectorAll('.btn-start-go-js'),
    $logLang = document.querySelector('.log-lang'),
    $headerProfile = document.querySelector('.header-profile');

let $countQuiz = 1,
    $phone_exists = false,
    $user_phone = '';

$sectionJs.classList.add('active-block');
$phoneJs.classList.add('active-flex');
$logLang.classList.add('active-flex');
$headerProfile.classList.remove('active-flex');

const quizGoSlide = (e) => {
    // e.preventDefault();

    if ($countQuiz === 1) {
        $sectionJs.classList.add('active-block');
        $phoneJs.classList.add('active-flex');
        $pinJs.classList.remove('active-flex');
        $logLang.classList.add('active-flex');
        $headerProfile.classList.remove('active-flex');
        if ($phoneJs.querySelector('input').value.length >= 1) {

            $countQuiz++;
            $bNums.textContent = $phoneJs.querySelector('input').value;
        }
    }

    if ($countQuiz === 2) {
        $phoneJs.classList.remove('active-flex');
        $pinJs.classList.add('active-flex');

        $hidInput.focus();

        $phoneJs.querySelector('input').value = '';

        $hidInput.addEventListener('input', () => {
            if ($hidInput.value.length === 4) {
                // $countQuiz = 3;
                //
                // setTimeout(() => {
                //     $hidInput.value = '';
                //     quizGoSlide(e);
                // }, 300);

            }
        });
    }

    if ($countQuiz === 3) {
        $sectionJs.classList.remove('active-block');
        $pinJs.classList.remove('active-flex');
        $sectionTypeAds.classList.add('active-block');
        $logLang.classList.remove('active-flex');
        $headerProfile.classList.add('active-flex');
    }

    if ($countQuiz === 4) {
        $sectionTypeAds.classList.remove('active-block');
        $sectionFill.classList.add('active');
    }

    if ($countQuiz === 5) {
        $sectionFill.classList.remove('active');
        $sectionSuccessfully.classList.add('active-flex');
    }
}, quiz_go_slide_send_phone = () => {
    $sectionJs.classList.add('active-block');
    $phoneJs.classList.add('active-flex');
    $pinJs.classList.remove('active-flex');
    $logLang.classList.add('active-flex');
    $headerProfile.classList.remove('active-flex');
    if ($phoneJs.querySelector('input').value.length >= 1) {
        $bNums.textContent = $phoneJs.querySelector('input').value;
    }
}, quiz_go_slide_send_sms = () => {
    $phoneJs.classList.remove('active-flex');
    $pinJs.classList.add('active-flex');

    $hidInput.focus();

    $phoneJs.querySelector('input').value = '';

    $hidInput.addEventListener('input', () => {
        if ($hidInput.value.length === 6) {
            setTimeout(() => {
                if ($phone_exists) {
                    auth($hidInput.value);
                } else {
                    reg($hidInput.value);
                }
                $hidInput.value = '';
            }, 300);
        }
    });
}, quiz_go_slide_home = () => {
    $sectionJs.classList.remove('active-block');
    $pinJs.classList.remove('active-flex');
    $sectionTypeAds.classList.add('active-block');
    $logLang.classList.remove('active-flex');
    $headerProfile.classList.add('active-flex');
}, send_sms = ($phone) => {
    $.ajax({
        method: 'POST',
        url: '/api/sms',
        data: {"phone": $phone},
        dataType: 'json',
        timeOut: 30000,
        success: function(response) {
            $('#user_number').text($phone);
            // show next slide
            quiz_go_slide_send_sms();
        }
    });
}, save_token = ($token) => {
    $.ajax({
        method: 'POST',
        url: '/token/set',
        data: {"token": $token, "_token": $_csrf_token},
        dataType: 'json',
        timeOut: 30000,
        success: function(response) {

        }
    });
}, reg = ($code) => {
    $.ajax({
        method: 'POST',
        url: '/api/reg',
        data: {"phone": $user_phone, "phone_code": $code, "language_id": 1, "region_id": 0},
        dataType: 'json',
        timeOut: 30000,
        success: function(response) {
            if (!response.token) {
                quiz_go_slide_send_sms();
            }
            // save token
            save_token(response.token);
            // show next slide
            quiz_go_slide_home();
        }
    });
}, auth = ($code) => {
    $.ajax({
        method: 'POST',
        url: '/api/login',
        data: {"phone": $user_phone, "phone_code": $code},
        dataType: 'json',
        timeOut: 30000,
        success: function(response) {
            if (!response.token) {
                quiz_go_slide_send_sms();
            }
            // save token
            save_token(response.token);
            // show next slide
            quiz_go_slide_home();
        }
    });
}


$btn_send_phone.forEach(btn => {
    btn.addEventListener('click', e => {
        e.preventDefault();
        $user_phone = $phone.val();
        if (!$user_phone) {
            return;
        }

        // check phone
        $.ajax({
            method: 'POST',
            url: '/api/check-phone',
            data: {"phone": $phone.val()},
            dataType: 'json',
            timeOut: 30000,
            success: function(response) {
                $phone_exists = response.result;
            }
        });

        // send sms
        send_sms($phone.val());
    });
});


$go_first_step.click(function (e) {
    e.preventDefault();
    quiz_go_slide_send_phone();
});


$send_sms_again.click(function (e) {
    e.preventDefault();
    send_sms($user_phone);
});


$harkBack.forEach(btnminus => {
    btnminus.addEventListener('click', e => {
        e.preventDefault();

        $countQuiz--;
        quizGoSlide(e);

    });
})


$boxTypeItem.forEach(item => {
    item.addEventListener('click', e => {
        e.preventDefault();
        $countQuiz++;
        quizGoSlide(e);

    });
});
$btnStartGoJs.forEach(item => {
    item.addEventListener('click', e => {
        e.preventDefault();
        $countQuiz = 1;
        quizGoSlide(e);
        $sectionSuccessfully.classList.remove('active-flex');
    });
});


const swiper = new Swiper('.swiper-container', {

    loop: false,
    breakpoints: {
        // when window width is >= 320px
        320: {
            slidesPerView: 2.5,
            spaceBetween: 8
        },
        // when window width is >= 480px
        480: {
            slidesPerView: 3,
            spaceBetween: 8
        },
        // when window width is >= 640px
        1000: {
            slidesPerView: 3,
            spaceBetween: 8
        }
    }

});
