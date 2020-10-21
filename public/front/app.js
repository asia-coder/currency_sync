axios.create({
  headers: {'Content-Type': 'application/x-www-form-urlencoded'}
});

axios.interceptors.request.use(
  async (config) => {
    const token = localStorage.getItem('token')
    if (token) config.headers.Authorization = 'Bearer ' + token
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
);

new Vue({
  el: '#app',
  vuetify: new Vuetify(),

  data: {
    form: {},
    auth: false,
    currency: null,
    total: null,
    page: 1,
    currencyItem: null
  },

  created() {
    this.currencies()
  },

  methods: {
    login() {
      let formData = new FormData();

      formData.set('email', this.form.email);
      formData.set('password', this.form.password);

      axios.post('/login', formData)
        .then((response) => {
          if (response.data.data.success) {
            localStorage.setItem('token', response.data.data.token)
            this.auth = true;
            this.currencies()
          } else if (!response.data.data.success) {
            localStorage.removeItem('token')
            this.auth = false;
          }
        })
        .catch((error) => {
          console.log(error)
        })
    },

    currencies() {
      axios.get('/currencies', {params: { page: this.page }})
        .then((response) => {
          if (response.status === 200) {
            this.auth = true;
            this.currency = response.data.data;
            this.total = response.data.total;
          } else if (response.status === 401) {
            this.auth = false;
            this.currency = null;
            this.total = null;
          }
        })
        .catch((error) => {
          console.log(error)
        })
    },

    getCurrency(id) {
      axios.get('/currency/' + id)
        .then((response) => {
          if (response.status === 200) {
            this.auth = true;
            this.currencyItem = response.data.data;
          } else if (response.status === 401) {
            this.auth = false;
            this.currencyItem = null;
          }
        })
        .catch((error) => {
          console.log(error)
        })
    },

    goList() {
      this.currencyItem = null;
    }
  },

  watch: {
    page(value) {
      this.currencies()
    }
  }
})
