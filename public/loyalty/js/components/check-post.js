import vue2Dropzone from 'vue2-dropzone'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'
import axios from 'axios'


export default {
  name: 'check-post',
  props: {
    hasUrl: {
      type: Number,
      default: 0
    },
    type: String
  },
  data () {
    return {
      dropzoneOptions: {
        url: window.URLS.CONFIRM_SHARE,
        maxFiles: 1,
        autoProcessQueue: false,
        thumbnailWidth: 50,
        thumbnailHeight: 50,
        previewTemplate: document.querySelector('#tpl').innerHTML,
        acceptedFiles: 'image/*',
        dictDefaultMessage: this.type === 'instagram' ? window.i18n.INSTAGRAM_ATTACH_FILE: window.i18n.TELEGRAM_ATTACH_FILE,
        dictInvalidFileType: window.i18n.DROPZONE_INVALID_FILE_TYPE,
        dictFallbackMessage: window.i18n.DROPZONE_NO_DRAG_AND_DROP,
        dictFileTooBig: window.i18n.DROPZONE_FILE_TOO_BIG,
      },
      isSend: false,
      percent: 0,
      timeLeft: 10,
      progressStyle: '',
      url: '',
      isLoading: false
    }
  },
  components: {
    vueDropzone: vue2Dropzone
  },
  filters: {
    integer: function (value) {
      if (!value) return ''
      return parseInt(value)
    }
  },
  methods: {
    vfileAdded () {
      if (this.$refs.myVueDropzone.dropzone.files[1] != null) {
        this.$refs.myVueDropzone.dropzone.removeFile( this.$refs.myVueDropzone.dropzone.files[0])
      }
    },

    verror () {
      setTimeout(() => {
        this.$refs.myVueDropzone.dropzone.removeAllFiles()
      }, 3000)
    },

    generateFormData (items) {
      let bodyFormData = new FormData()

      items.forEach(item => bodyFormData.set(item.key, item.value))

      return bodyFormData
    },

    onBeforeFormSubmit () {
      if (this.url || this.$refs.myVueDropzone.dropzone.files.length) {
        this.onFormSubmit()
      } else {
        window.swal('', window.i18n.SHARE_FORM_VALIDATION_ERROR, 'error')
      }
    },

    onFormSubmit () {
      let data = this.generateFormData([
        { key: 'type', value: this.type }
      ])

      if (this.hasUrl) {
        data.set('url', this.url)
      }

      if (this.$refs.myVueDropzone && this.$refs.myVueDropzone.dropzone.files.length) {
        data.set('image', this.$refs.myVueDropzone.dropzone.files[0])
      }

      this.isLoading = true

      axios({
        method: 'post',
        url: window.URLS.CONFIRM_SHARE,
        data,
        config: { headers: {'Content-Type': 'multipart/form-data' }}
      })
      .then(response => response.data.data)
      .then(data => {
        const { requiresModeration, autoConfirmTime, transactionId } = data

        if (requiresModeration) {
          window.loyalty.modalManager.openTextModal(window.i18n.SHARE_REQUIRES_MODERATION)
          this.clearFields()
        } else {
          this.isSend = true

          this.showProgressBar(autoConfirmTime).then(() => {

            axios({
              method: 'post',
              url: window.URLS.SHARE_CHECK_TRANSACTION_STATUS,
              data: this.generateFormData(
                [
                  { key: 'transaction_id', value: transactionId }
                ]
              ),
              config: { headers: {'Content-Type': 'multipart/form-data' }}
            })
            .then(response => response.data.data)
            .then(data => {
              const { success, reward } = data

              if (success) {
                window.loyalty.modalManager.openTextModal(window.i18n.SHARE_VALIDATION_SUCCESS.replace('%r%', reward))
                window.loyalty.updateEverything()
              } else {
                window.swal('', window.i18n.SHARE_VALIDATION_ERROR, 'error')
              }

              this.clearFields()
            })
          })
        }
      })
      .finally(() => {
        this.isLoading = false
      })
    },

    showProgressBar (timeout) {
      return new Promise(resolve => {
        this.timeLeft = timeout / 1000

        let stepTime = this.timeLeft / 20

        const timer = setInterval(() => {
          if (this.percent === 100) {
            clearInterval(timer)
            this.percent = 0
            this.timeLeft = timeout / 1000
            this.url = ''
            this.progressStyle = ''
            this.isSend = false
            resolve()
          }

          this.percent += 5
          this.timeLeft -= stepTime
          this.progressStyle = `background-size: ${this.percent}% 100%`
        }, timeout / 20)
      })
    },

    clearFields () {
      this.url = ''
      this.$refs.myVueDropzone && this.$refs.myVueDropzone.dropzone.removeAllFiles()
    }
  }
}