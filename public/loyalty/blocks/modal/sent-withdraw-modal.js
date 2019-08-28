export default class SentWithdrawModal {
    constructor() {
        this.el = document.querySelector('.js-sent-withdraw-modal');

        if (!this.el) {
            return
        }

        this.close = this.close.bind(this);
    }


    close() {
        window.loyalty.modalManager.closeAll();
    }
}
