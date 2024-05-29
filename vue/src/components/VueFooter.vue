<template>
  <footer class="footer-box">
    <div class="container">
      <div class="footer">
        <div class="footer__left">
          <div href="https://tattoofeel.ru" class="footer__left__logo">
            <svg class="icon" width="63px" height="63px" fill="#fff">
              <use xlink:href="/img/svg/icons.svg#logo"></use>
            </svg>
          </div>
          <div class="footer__left__desc">
            <div class="footer__left__desc__soc">
              <a target="_blank" href="https://t.me/tattoofeel_ru"
                ><svg class="icon" width="16px" height="16px" fill="#363636">
                  <use xlink:href="/img/svg/icons.svg#telega"></use>
                </svg> </a
              ><a target="_blank" href="https://vk.com/tattoofeel_ru"
                ><svg class="icon" width="16px" height="16px" fill="#363636">
                  <use xlink:href="/img/svg/icons.svg#vk"></use>
                </svg>
              </a>
            </div>
          </div>
        </div>
        <div class="footer__pay">
          <div class="footer__pay__img">
            <a href="/pay-card/" class="img-master-visa"></a>
          </div>
          <div class="footer__pay__link">
            <a href="/payment/">Оплата</a><a href="/warranty/">Гарантии</a>
          </div>
        </div>
        <div class="footer__spr__contact"></div>
        <div class="footer__contact">
          <a class="footer__contact__mail" href="mailto:operator@tattoofeel.ru"
            ><svg
              class="icon"
              width="22px"
              height="22px"
              fill="#F8CD4F"
              stroke="#F8CD4F"
            >
              <use xlink:href="/img/svg/icons.svg#mail"></use>
            </svg>
            operator@tattoofeel.ru </a
          ><span class="footer__contact__time"
            ><svg
              class="icon"
              width="22px"
              height="22px"
              fill="#F8CD4F"
              stroke="#F8CD4F"
            >
              <use xlink:href="/img/svg/icons.svg#time"></use>
            </svg>
            Пн-Вс 09:00-21:00
          </span>
        </div>
        <div class="footer__spr__contact__phone"></div>
        <div class="footer__contact footer__contact__phone">
          <a
            rel="nofollow"
            class="footer__contact__tel"
            href="tel:89585781048"
            onclick="ym(73251517,'reachGoal','phone_footer'); return true;"
            ><svg
              class="icon"
              width="22px"
              height="22px"
              fill="#F8CD4F"
              stroke="#F8CD4F"
            >
              <use xlink:href="/img/svg/icons.svg#phone"></use>
            </svg>
            8 (958) 578-10-48 </a
          ><span class="footer__contact__address"
            ><svg
              class="icon"
              width="22px"
              height="22px"
              fill="#F8CD4F"
              stroke="#F8CD4F"
            >
              <use xlink:href="/img/svg/icons.svg#point"></use>
            </svg>
            г. Москва, Электродная ул.,<br />дом 2, стр. 33, 3 этаж,<br />территория
            тату-студии Barn house tattoo
          </span>
        </div>
        <ul class="footer__menu">
          <li><a href="/catalog/">Каталог</a></li>
          <li><a href="/article/">Статьи</a></li>
          <li><a href="/contact/">Контакты</a></li>
          <li><a href="/brands/">Бренды</a></li>
          <li><a href="/news/">Новости</a></li>
          <li><a href="/delivery/">Доставка</a></li>
          <li><a href="/stock/">Акции</a></li>
          <li><a href="/team/">Наша команда</a></li>
        </ul>
        <div class="footer__copy">
          Индивидуальный предприниматель Волков&nbsp;Иван&nbsp;Евгеньевич<br />
          Свидетельство о государственной регистрации №&nbsp;321673300035122 от
          4 октября 2021 года.<br />ИНН: 673111423375
          <address>
            ©&nbsp;Copyright&nbsp;2023 tattoofeel.ru. All Rights Reserved.
          </address>
        </div>
        <div class="footer__btn">
          <a class="btn _wide js-show-reviews-form" href="#"
            >Что нам улучшить ?</a
          ><a class="footer__btn__link" href="/privacy-policy/"
            >Политика конфиденциальности</a
          >
        </div>
      </div>
    </div>
    <div id="toTop" style="display: block">
      <div class="arrow-top"></div>
    </div>
    <div class="my-modal">
      <div id="reviews-form" class="modal-form"></div>
    </div>
    <div class="my-modal">
      <div id="not-found-search-form" class="modal-form"></div>
    </div>
    <div class="my-modal">
      <div id="select-your-city-form" class="modal-form">
        <div class="modal-close"></div>
        <div class="contact-form__fields">
          <input type="text" class="search-select-your-city" />
          <div class="select-your-city-list">
            <div class="select-your-city-col">
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://astraxan.tattoofeel.ru"
                  >Астрахань</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://barnaul.tattoofeel.ru"
                  >Барнаул</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://belgorod.tattoofeel.ru"
                  >Белгород</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://bryansk.tattoofeel.ru"
                  >Брянск</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://vladivostok.tattoofeel.ru"
                  >Владивосток</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://volgograd.tattoofeel.ru"
                  >Волгоград</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://voronezh.tattoofeel.ru"
                  >Воронеж</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://ekaterinburg.tattoofeel.ru"
                  >Екатеринбург</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://ivanovo.tattoofeel.ru"
                  >Иваново</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://izhevsk.tattoofeel.ru"
                  >Ижевск</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://irkutsk.tattoofeel.ru"
                  >Иркутск</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://kazan.tattoofeel.ru"
                  >Казань</a
                >
              </div>
            </div>
            <div class="select-your-city-col">
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://kaliningrad.tattoofeel.ru"
                  >Калининград</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://kemerovo.tattoofeel.ru"
                  >Кемерово</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://kirov.tattoofeel.ru"
                  >Киров</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://krasnoyarsk.tattoofeel.ru"
                  >Красноярск</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://kursk.tattoofeel.ru"
                  >Курск</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://lipeck.tattoofeel.ru"
                  >Липецк</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://magnitogorsk.tattoofeel.ru"
                  >Магнитогорск</a
                >
              </div>
              <div class="select-your-city-item">
                <a class="select-your-city-link" href="https://tattoofeel.ru"
                  >Москва</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://naberezhnye-chelny.tattoofeel.ru"
                  >Набережные Челны</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://nnovgorod.tattoofeel.ru"
                  >Нижний Новгород</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://nizhnij-tagil.tattoofeel.ru"
                  >Нижний Тагил</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://novokuzneck.tattoofeel.ru"
                  >Новокузнецк</a
                >
              </div>
            </div>
            <div class="select-your-city-col">
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://novosibirsk.tattoofeel.ru"
                  >Новосибирск</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://omsk.tattoofeel.ru"
                  >Омск</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://orenburg.tattoofeel.ru"
                  >Оренбург</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://penza.tattoofeel.ru"
                  >Пенза</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://perm.tattoofeel.ru"
                  >Пермь</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://rostov-na-donu.tattoofeel.ru"
                  >Ростов-на-Дону</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://samara.tattoofeel.ru"
                  >Самара</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://sankt-peterburg.tattoofeel.ru"
                  >Санкт-Петербург</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://saratov.tattoofeel.ru"
                  >Саратов</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://sochi.tattoofeel.ru"
                  >Сочи</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://stavropol.tattoofeel.ru"
                  >Ставрополь</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://tver.tattoofeel.ru"
                  >Тверь</a
                >
              </div>
            </div>
            <div class="select-your-city-col">
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://tolyatti.tattoofeel.ru"
                  >Тольятти</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://tomsk.tattoofeel.ru"
                  >Томск</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://tula.tattoofeel.ru"
                  >Тула</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://tyumen.tattoofeel.ru"
                  >Тюмень</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://ulan-ude.tattoofeel.ru"
                  >Улан-Удэ</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://ulyanovsk.tattoofeel.ru"
                  >Ульяновск</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://ufa.tattoofeel.ru"
                  >Уфа</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://cheboksary.tattoofeel.ru"
                  >Чебоксары</a
                >
              </div>
              <div class="select-your-city-item">
                <a
                  class="select-your-city-link"
                  href="https://chelyabinsk.tattoofeel.ru"
                  >Челябинск</a
                >
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>
<style lang="scss" scoped>
.container {
  max-width: calc(1440px + 32px);
  padding-left: 16px;
  padding-right: 16px;
  margin: 0 auto;
}
</style>
