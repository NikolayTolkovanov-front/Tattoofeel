const url = new URL('http://tattoofeel2.ru')

class AuthService {
  async sendPhone(phone) {
    const newUrl = new URL('vue-api/auth/send-sms/', url)
    newUrl.searchParams.append('phone', phone)

    try {
      const res = await fetch(newUrl, {
        method: 'GET',
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })

      const result = await res.json()
      return result
    } catch (error) {
      throw new Error(error)
    }
  }
  async sendCode(code) {
    const newUrl = new URL('vue-api/auth/login/', url)
    newUrl.searchParams.append('code', code)

    try {
      const res = await fetch(newUrl, {
        method: 'GET',
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })

      const result = await res.json()
      return result
    } catch (error) {
      throw new Error(error)
    }
  }
}

export default new AuthService