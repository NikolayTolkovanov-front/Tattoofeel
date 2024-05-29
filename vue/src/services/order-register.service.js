const url = new URL("http://tattoofeel2.ru");

class OrderRegisterService {
  async checkoutInit(token) {
    const newUrl = new URL("vue-api/cart/checkout-init/", url);

    try {
      const res = await fetch(newUrl, {
        method: "GET",
        headers: {
          Authorization: `Bearer ${token}`,
          "X-Requested-With": "XMLHttpRequest",
        },
      });

      const data = await res.json();
      return data;
    } catch (error) {
      throw new Error(error);
    }
  }
  async getCities(token, cityFragment) {
    const newUrl = new URL("vue-api/delivery/get-cities/", url);
    newUrl.searchParams.append("q", cityFragment);
    newUrl.searchParams.append("ds", "cdek");

    console.log("here:", newUrl.toString());

    try {
      const res = await fetch(newUrl, {
        method: "GET",
        headers: {
          Authorization: `Bearer ${token}`,
          "X-Requested-With": "XMLHttpRequest",
        },
      });

      const result = await res.json();
      return result;
    } catch (error) {
      throw new Error(error);
    }
  }
  async getCityCodes(token, cityId, cityName) {
    const newUrl = new URL("lk/get-city-codes/?cdek_city_id=44&cdek_city_name=%D0%9C%D0%BE%D1%81%D0%BA%D0%B2%D0%B0%2C+%D0%A0%D0%BE%D1%81%D1%81%D0%B8%D1%8F", url);
    // newUrl.searchParams.append("cdek_city_id", 44);
    // newUrl.searchParams.append("cdek_city_name", "Москва, Россия");

    console.log(newUrl.toString());
    try {
      const res = await fetch(newUrl, {
        method: "GET",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          Authorization: `Bearer ${token}`,
        },
      }).then((res) => console.log("res:", res));

      const result = await res.json();
      return result;
    } catch (error) {
      throw new Error(error);
    }
  }
  async getCdekPvzInfo(token, cityCode) {
    const newUrl = new URL("lk/get-sdek-pvz-info/", url);
    newUrl.searchParams.append("cdek_city_code", 44);

    try {
      const res = await fetch(newUrl, {
        method: "GET",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          Authorization: `Bearer ${token}`,
        },
      }).then((res) => console.log("res:", res));

      const result = await res.json();
      return result;
    } catch (error) {
      throw new Error(error);
    }
  }
  async getPvzList(token, deliveryService, cityName) {
    const newUrl = new URL("lk/pvz/", url);
    newUrl.searchParams.append("ds", "cdek");
    newUrl.searchParams.append("city_name", "Москва, Россия");

    try {
      const res = await fetch(newUrl, {
        method: "GET",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          Authorization: `Bearer ${token}`,
        },
      }).then((res) => console.log("res:", res));

      const result = await res.json();
      return result;
    } catch (error) {
      throw new Error(error);
    }
  }
}

export default new OrderRegisterService();
