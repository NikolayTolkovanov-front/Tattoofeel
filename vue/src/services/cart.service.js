const url = new URL('http://tattoofeel2.ru')

class CartService {
  async getCart() {
    const newUrl = new URL('catalog/get-cart', url)

    try {
      const res = await fetch(newUrl, {
        method: 'GET',
      })
  
      const cart = await res.json()
      return cart;
    } catch (error) {
      throw new Error(error)
    }
  }
  async addProduct(id, count) {
    const newUrl = new URL('catalog/add-cart/', url)
    newUrl.searchParams.append('count', count)
    newUrl.searchParams.append('id', id)

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
  async addProductEcommerce(id, count) {
    const newUrl = new URL('catalog/add-products-ecommerce/', url)
    newUrl.searchParams.append('id', id)
    newUrl.searchParams.append('count', count)

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
  async changeProduct(id, count, coupon) {
    const newUrl = new URL('catalog/change-cart/', url)
    newUrl.searchParams.append('count', count)
    newUrl.searchParams.append('id', id)
    // newUrl.searchParams.append('coupon_code', coupon)
    
    if (coupon.length) {
      newUrl.searchParams.append('coupon_code', coupon)
    }

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
  async changeProductEcommerce(id, count, coupon) {
    const newUrl = new URL('catalog/change-products-ecommerce/', url)
    newUrl.searchParams.append('count', count)
    newUrl.searchParams.append('id', id)
    // newUrl.searchParams.append('coupon_code', coupon)
    
    if (coupon.length) {
      newUrl.searchParams.append('coupon_code', coupon)
    }

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
  async removeProduct(id, coupon) {
    const newUrl = new URL('catalog/remove-cart/', url)
    newUrl.searchParams.append('id', id)
    // newUrl.searchParams.append('coupon_code', coupon)
    if (coupon.length) {
      newUrl.searchParams.append('coupon_code', coupon)
    }

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
  async removeProductEcommerce(id, coupon) {
    const newUrl = new URL('catalog/remove-products-ecommerce/', url)
    newUrl.searchParams.append('id', id)
    // newUrl.searchParams.append('coupon_code', coupon)

    if (coupon.length) {
      newUrl.searchParams.append('coupon_code', coupon)
    }

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

export default new CartService