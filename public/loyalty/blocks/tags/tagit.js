'use strict';

export default class TagIt {
  constructor(el) {
    this.el = el;
    this.input = document.querySelector('.js-invite-emails');
    this.hiddenInput = this.el.querySelector('.js-tagit-hidden-input');
    this.list = this.el.querySelector('.js-tagit-list');
    this.items = [];

    this.onKeyUp = this.onKeyUp.bind(this);
    this.onKeyDown = this.onKeyDown.bind(this);
    this.onFocus = this.onFocus.bind(this);
    this.onBlur = this.onBlur.bind(this);
    this.renderTags = this.renderTags.bind(this);
    this.deleteTag = this.deleteTag.bind(this);
    this.addEventListeners = this.addEventListeners.bind(this);
    this.onClickList = this.onClickList.bind(this);
    this.setResultInputValue = this.setResultInputValue.bind(this);
    this.reset = this.reset.bind(this);

    this.list.addEventListener('click', this.onClickList);

    this.addEventListeners();
  }

  onClickList() {
    if (!this.el.classList.contains('is-focused')) {
      this.hiddenInput.focus();
    }
  }

  addEventListeners() {
    this.hiddenInput.addEventListener('keydown', this.onKeyDown);
    this.hiddenInput.addEventListener('keyup', this.onKeyUp);
    this.hiddenInput.addEventListener('focus', this.onFocus);
    this.hiddenInput.addEventListener('blur', this.onBlur);

    [].forEach.call(this.el.querySelectorAll('.js-remove-tag'), item => {
      item.addEventListener('click', this.deleteTag);
    });
  }

  onKeyDown(e) {
    if (e.keyCode == 13) {
      e.preventDefault();
      e.stopPropagation();
    }

    if (e.keyCode == 8 && !e.target.value.length && this.items.length) {
      this.items.pop();
      this.renderTags();
      this.hiddenInput.focus();
    }
    this.hiddenInput.setAttribute('size', e.target.value.length + 3);
  }

  onKeyUp(e) {
    if (e.keyCode == 32 || e.keyCode == 188 || e.keyCode == 13) {
      let item =  this.normalizeString(e.target.value);
      if (item) {
        this.items.push(item);
        this.hiddenInput.value = '';
        this.hiddenInput.setAttribute('size', 1);
      }
      this.renderTags();
      this.hiddenInput.focus();
    }
  }

  normalizeString(str) {
    return str.replace(',', '').trim();
  }

  onFocus() {
    this.el.classList.add('is-focused');
  }

  onBlur() {
    this.el.classList.remove('is-focused');
    if (this.hiddenInput.value) {
      this.items.push(this.normalizeString(this.hiddenInput.value));
      this.renderTags();
    }
  }

  renderTag(item) {
    let li = document.createElement('li');
    li.innerHTML = `${item} <svg data-key=${item} class="tags__remove js-remove-tag"><use xlink:href="#popup-close"></use></svg>`;
    li.classList.add('tags__item');
    li.classList.add('c-primary-bg');
    return li;
  }

  renderTags() {
    if (this.items.length) {
      this.el.classList.add('is-filled');
    } else {
      this.el.classList.remove('is-filled');
    }

    let tags = this.items.map(this.renderTag);

    this.list.innerHTML = '';

    this.hiddenInput.value = '';
    tags.forEach((item, i) => this.list.insertBefore(item, this.list.childNodes[i]));
    this.addHiddenInput();
    this.setResultInputValue();
  }

  setResultInputValue() {
    this.input.value = this.items.join(',');
  }

  addHiddenInput() {
    let li = document.createElement('li'),
        input  = document.createElement('input');

    li.classList.add('tags__item');
    li.classList.add('tags__item_content_input');
    input.classList.add('tags__input');
    input.classList.add('js-tagit-hidden-input');
    input.setAttribute('size', 1);

    li.appendChild(input);
    this.hiddenInput = input;
    this.addEventListeners();
    this.list.appendChild(li);
  }

  deleteTag(e) {
    let target = e.target.closest('.js-remove-tag');

    if (!target) return;

    e.preventDefault();
    let key = target.getAttribute('data-key');
    this.items.splice(this.findDeleteItemIndex(key), 1)
    this.list.removeChild(target.parentNode);
    this.setResultInputValue();
  }

  findDeleteItemIndex(key) {
    let index;
    for (let i = 0; i < this.items.length; i++) {
      if (this.items[i] === key) {
        index = i;
        break;
      }
    }
    return index;
  }

  reset() {
    this.items = [];
    this.renderTags();
  }
}
