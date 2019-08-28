'use strict';

import Global from 'global';

export default class UploadImage {
  constructor(el) {
    this.el = el;
    this.fileInput = el.querySelector('.js-picture');
    this.form = el.querySelector('.js-picture-form');
    this.avatarImage = el.querySelector('.js-avatar');
    this.pictureUrlInput = el.querySelector('.js-picture-url');
    this.removeAvatarButton = el.querySelector('.js-avatar-remove');

    this.onChange = this.onChange.bind(this);
    this.setPicture = this.setPicture.bind(this);
    this.onRemove = this.onRemove.bind(this);

    this.fileInput.addEventListener('change', this.onChange);
    this.removeAvatarButton.addEventListener('click', this.onRemove);
  }

  onChange() {
    let formData = new FormData(this.form);

    let xhr = new XMLHttpRequest();
    xhr.open(this.form.getAttribute('method'), this.form.getAttribute('action'));
    xhr.onreadystatechange = () => {
      if (xhr.readyState == 4) {
        if (xhr.status == 200) {
          let response = JSON.parse(xhr.responseText);
            if (response.type == 'data') {
              this.setPicture(response.data.url);
            } else {
              Global.processAjaxResponse(response);
            }
        }
      }
    }

    xhr.send(formData);
  }

  setPicture(url) {
    this.el.classList.add('is-image-loaded');
    this.avatarImage.setAttribute('src', url);
    this.pictureUrlInput.value = url;
  }

  onRemove(e) {
    e.preventDefault();
    this.el.classList.remove('is-image-loaded');
    this.avatarImage.setAttribute('src', '');
    this.pictureUrlInput.value = '';
  }
}
