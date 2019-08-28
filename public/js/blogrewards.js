if (window.location.pathname === '/') {
  window.location.href = 'https://blogrewards.bitrewards.com/brands/campaigns/new';
}

var isLoaded = false;
var authTryCounter = 0;
var timer = setTimeout(function changeText() {
  var link = document.querySelector("link[rel*='icon']") || document.createElement('link');
    link.type = 'image/png';
    link.rel = 'shortcut icon';
    link.href = 'https://bitrewards.network/favicon-32x32.png';
    document.getElementsByTagName('head')[0].appendChild(link);

  document.title = document.title.replace('AdHive', 'BlogRewards');

  var footer = document.querySelector('.b-footer__text')
  if (footer) {
    footer.innerHTML = "Whilst every effort has been made to ensure that the details contained herein are correct and up-to-date, it does not constitute legal or other professional advice. BitRewards does not accept any responsibility, legal or otherwise, for any errors or omission. <br>© 2019. BitRewards, all rights reserved";
    footer.classList.add('is-show')
    // isLoaded = true
  }

  var links = document.querySelectorAll('.b-footer__link')

  links.forEach(function (item) {
    item.classList.add('is-show');

    if (item.innerText === 'Политика конфиденциальности') {
      item.href = 'https://docs.google.com/document/d/e/2PACX-1vT0tsBImOFCsflctsXwpGW7RbuxmYAItnVplAAZ_qaZzT7C3wfvdbZl0R05Et6A08d8j0z1tIwW8Dzv/pub?embedded=true';
      item.innerText = 'Политика обработки персональных данных';
    }

    if (item.innerText === 'Пользовательское соглашение') {
      item.href = 'https://docs.google.com/document/d/e/2PACX-1vTury0uBaBvkqTH2GuC4IzM-DscCSnBR-B7JUbFDERw9bDYtYc_mTIBUKd1s5IzD3D4BX6f-QDm_08K/pub?embedded=true'
      item.innerText = 'Конфиденциальность персональной информации'
    }

    if (item.innerText === 'Политика возврата') {
      item.href = 'https://docs.google.com/document/d/12nYfHzk0lLx3nC03aT_mc1F9YXH_9QAt46T24kQqI68/pub?embedded=true';
      item.innerText = 'Пользовательское соглашение';
    }

    if (item.innerText === 'Контакты' || item.innerText === 'Contacts') {
      item.href = 'mailto:ad@bitrewards.com'
    }

    if (item.innerText === 'Privacy Policy') {
      item.innerText = 'Privacy';
      item.href = 'https://bitrewards.network/brw-privacy-policy.pdf'
    }

    if (item.innerText === 'Terms of use') {
      item.innerText = 'Terms & Conditions';
      item.href = 'https://bitrewards.network/brw-terms-of-use-for-website.pdf';
    }

    if (item.innerText === 'Refund Policy') {
      item.style.display = 'none'
    }
  });

  var faq = document.querySelectorAll('.section-title');

  faq.forEach(function (item) {
    item.classList.add('is-show');
    item.innerText = item.innerText.replace('Adhive', 'BitRewards').replace('AdHive', 'BitRewards');
  });

  var contact = document.querySelectorAll('.contacts__btn')
  contact.forEach(function (item) {
    if (item.innerText === 'Почта' || item.innerText === 'Email') {
      item.href = 'mailto:ad@bitrewards.com';
    }
  })

  var inputs = document.querySelectorAll('textarea, input')
  inputs.forEach(function (item) {
    if (item.getAttribute('placeholder')) { 
      item.setAttribute('placeholder', item.getAttribute('placeholder').replace('Adhive', 'BitRewards').replace('AdHive', 'BitRewards').replace('#adhive', '#bitrewards').replace('@adhive.ru', '@bitrewards').replace('https://adhive.com/', 'https://bitrewards.com/'))
    }
  })

  var jivosite = document.querySelectorAll('jdiv')
  jivosite.forEach(function (item) {
    item.style.display = 'none';
  });

  var doc = document.querySelectorAll('#dialog-registration_terms-link')

  doc.forEach(function (item) {
    if (item.innerText == ' terms of use ') {
      item.href = 'https://bitrewards.network/brw-terms-of-use-for-website.pdf'
    } else {
      item.href = 'https://docs.google.com/document/d/12nYfHzk0lLx3nC03aT_mc1F9YXH_9QAt46T24kQqI68/pub?embedded=true'
    }
  });

  var adh = document.querySelectorAll('.value');
  adh.forEach(function (item) {
    item.innerText = item.innerText.replace('ADH', 'BIT')
  });

  if (!window.location.search.includes('bitrewards-auto-login')) {
    document.body.classList.add('is-auth');
  }

  if (isLoaded) {
    // clearTimeout(timer)
  } else {
    timer = setTimeout(changeText, 1000)
  }
}, 1000);
