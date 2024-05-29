<template>
  <div>
    <header class="header">
      <div class="container">
        <div class="header-inner">
          <div class="header__name">
            Интернет магазин для мастеров татуировки и татуажа в
            <span>Москве</span>
          </div>
          <div class="header__phone">
            <div class="header-address">
              <svg
                class="icon"
                width="20px"
                height="20px"
                fill="#363636"
                stroke="#363636"
              >
                <use xlink:href="/img/svg/icons.svg#point"></use>
              </svg>
              <div class="contact__block-data__desc">
                Адрес: г. Москва, Электродная ул.,<br />дом 2, стр. 33, 3
                этаж,<br />территория тату-студии Barn house tattoo
              </div>
              <div class="contact__block-data__desc">
                Подписывайтесь: <a target="_blank" href="">Telegram</a> |
                <a target="_blank" href="https://vk.com/tattoofeel_ru">VK</a>
              </div>
              <div class="contact__block-data__desc">
                Ваш город: Москва
                <a
                  href="javascript:void(0);"
                  class="your-city js-select-your-city"
                  >Изменить</a
                >
              </div>
            </div>
          </div>
          <div class="header__phone">
            <div class="header-phone">
              <a
                rel="nofollow"
                href="tel:89585781048"
                class="header-phone__number"
                onclick="ym(73251517,'reachGoal','phone_header'); return true;"
                ><svg
                  class="icon"
                  width="20px"
                  height="20px"
                  fill="#363636"
                  stroke="#363636"
                >
                  <use xlink:href="/img/svg/icons.svg#phone"></use>
                </svg>
                8 (958) 578-10-48
              </a>
              <div class="header-phone__time">Только звонки</div>
              <a
                target="_blank"
                rel="nofollow"
                href="https://wa.me/+79152968604"
                class="header-phone__number"
                onclick="ym(73251517,'reachGoal','phone_header'); return true;"
                ><svg
                  style="
                    position: absolute;
                    left: 0px;
                    width: 20px;
                    height: auto;
                    top: 45px;
                  "
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <g clip-path="url(#clip0_1047_967)">
                    <path
                      fill-rule="evenodd"
                      clip-rule="evenodd"
                      d="M11.9965 0C5.38113 0 0 5.38264 0 11.9999C0 14.6242 0.846321 17.058 2.285 19.0335L0.789795 23.4919L5.40222 22.0178C7.2993 23.2735 9.56321 24 12.0035 24C18.6189 24 24 18.6172 24 12.0001C24 5.38285 18.6189 0.000198364 12.0035 0.000198364L11.9965 0ZM8.64642 6.09541C8.41374 5.53814 8.23739 5.51704 7.88489 5.5027C7.76486 5.49573 7.63111 5.48877 7.48282 5.48877C7.02422 5.48877 6.54473 5.62276 6.25552 5.91902C5.90302 6.27879 5.02841 7.11819 5.02841 8.8396C5.02841 10.561 6.28379 12.2259 6.45297 12.4588C6.62932 12.6914 8.90039 16.2752 12.4266 17.7357C15.1842 18.8786 16.0025 18.7726 16.63 18.6387C17.5468 18.4412 18.6965 17.7636 18.9857 16.9455C19.2749 16.127 19.2749 15.4286 19.1901 15.2805C19.1055 15.1323 18.8726 15.0479 18.5201 14.8713C18.1676 14.6949 16.4537 13.8483 16.1292 13.7354C15.8118 13.6156 15.5086 13.658 15.269 13.9967C14.9304 14.4693 14.599 14.9492 14.3309 15.2383C14.1193 15.464 13.7736 15.4923 13.4846 15.3722C13.0966 15.2102 12.0107 14.8289 10.6705 13.6367C9.63369 12.7127 8.92848 11.5629 8.72406 11.2172C8.51944 10.8646 8.70294 10.6598 8.86496 10.4694C9.04131 10.2506 9.21052 10.0955 9.38687 9.89084C9.56322 9.68636 9.66195 9.58045 9.77481 9.34053C9.89483 9.10778 9.81003 8.86787 9.72543 8.69147C9.64084 8.51507 8.93563 6.79365 8.64642 6.09541Z"
                      fill="#363636"
                    ></path>
                  </g>
                  <defs>
                    <clipPath id="clip0_1047_967">
                      <rect width="24" height="24" fill="white"></rect>
                    </clipPath>
                  </defs>
                </svg>
                8 (915) 296-86-04
              </a>
              <div class="header-phone__time">Только Whatsapp</div>
              <div class="header-phone__time">Пн-Вс 09:00-21:00</div>
            </div>
          </div>
          <div class="header__btn">
            <div class="header__btn__soc">
              <a target="_blank" href=""
                ><svg class="icon" width="23px" height="23px" fill="#fff">
                  <use xlink:href="/img/svg/icons.svg#telega"></use>
                </svg> </a
              ><a target="_blank" href="https://vk.com/tattoofeel_ru"
                ><svg class="icon" width="23px" height="23px" fill="#fff">
                  <use xlink:href="/img/svg/icons.svg#vk"></use>
                </svg>
              </a>
            </div>
            <a class="btn" @click="showLoginModal"
              >Вход
              <span class="header__btn__ext-text">в личный кабинет</span></a
            >
          </div>
        </div>
      </div>
    </header>
    <div class="menu">
      <div class="container">
        <div class="menu-inner">
          <div class="menu__top__mob">
            <div>
              <i
                @click="toogleMobile"
                class="fa fa-bars"
                aria-hidden="true"
              ></i>
              <div class="menu__top__mob__sandwich">
                <div>TATTOOFEEL.RU</div>
              </div>
            </div>
            <div class="menu__top__mob__btn">
              <a href="#" class="icn-magnifier activate-search-mob"></a
              ><a @click="showLoginModal">Вход </a
              ><a href="/lk/cart/" style="position: relative"
                ><svg class="icon" width="24px" height="24px" fill="#363636">
                  <use xlink:href="/img/svg/icons.svg#cart"></use>
                </svg>
                <div class="menu-cart__count">0</div></a
              >
            </div>
          </div>
          <div class="menu__logo">
            <div href="" class="menu-logo">
              <svg class="icon" width="90px" height="90px">
                <use xlink:href="/img/svg/icons.svg#logo-top"></use>
              </svg>
            </div>
          </div>
          <div class="menu__right">
            <div class="menu__right__top">
              <div class="menu__right__top__left">
                <form
                  id="header-menu-search"
                  class="menu-search"
                  action="/catalog/search/"
                  method="get"
                >
                  <input
                    type="text"
                    id="menu-search"
                    class="placeholder -init ui-autocomplete-input"
                    name="q"
                    placeholder="Название или код товара..."
                    autocomplete="off"
                  />
                  <svg
                    class="icon icon-loader"
                    width="18px"
                    height="18px"
                    viewBox="22 22 44 44"
                    xmlns="http://www.w3.org/2000/svg"
                    style="animation: loader-rotate 1.4s linear infinite"
                  >
                    <circle
                      style="
                        animation: loader-dash 1.4s ease-in-out infinite;
                        stroke-dasharray: 80px, 200px;
                        stroke-dashoffset: 0;
                      "
                      cx="44"
                      cy="44"
                      r="20.2"
                      fill="none"
                      stroke-width="4"
                    ></circle>
                  </svg>
                  <input class="btn" type="submit" value="Найти" /><button
                    class="btn-mob"
                    type="submit"
                  ></button>
                </form>
                <span
                  :style="{ display: isMobileOpened ? 'flex' : 'none' }"
                  class="lupa"
                  style="
                    position: absolute;
                    right: 0px;
                    z-index: 9999;
                    background: rgb(255, 255, 255) none repeat scroll 0% 0%;
                    top: 10px;
                    width: 34px;
                    height: 34px;
                    display: none;
                    justify-content: center;
                    align-items: center;
                    border-radius: 50%;
                    cursor: pointer;
                  "
                  ><i class="fa fa-search" aria-hidden="true"></i
                ></span>
              </div>
              <div class="menu__right__top__right">
                <a href="/lk/cart/" class="menu-cart"
                  ><div class="menu-cart__icon">
                    <svg class="icon" width="32px" height="32px" fill="#363636">
                      <use xlink:href="/img/svg/icons.svg#cart"></use>
                    </svg>
                  </div>
                  <div class="menu-cart__count">{{ getTotalCount }}</div>
                  <div
                    class="menu-cart__sum"
                    :class="{ none: getTotalPrice === 0 }"
                    id="cart-sum"
                  >
                    {{ getTotalPrice.toLocaleString("ru-RU") }} ₽
                  </div></a
                >
              </div>
            </div>
            <div class="menu-nav" :class="{ open__mobile: isMobileOpened }">
              <div class="container-DOWN-MD">
                <ul class="menu-nav__list">
                  <p class="toogle__menu" @click="isOpenedList = !isOpenedList">
                    <svg
                      :class="{ caret_deg : isOpenedList }"
                      width="18"
                      height="12"
                      viewBox="0 0 18 12"
                      fill="none"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        d="M2 2L9 10L16 2"
                        stroke="#363636"
                        stroke-width="2.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      />
                    </svg>
                  </p>
                  <li>
                    <a href="/catalog/">Каталог</a>
                    <ul class="menu-nav__list__sub">
                      <li class="category">
                        <a class="category-name" href="/catalog/tatu-masinki/"
                          ><span
                            style="background-image: url(/img/default.png)"
                          ></span>
                          category одежда</a
                        >
                        <ul class="types">
                          <li class="types-item">
                            <a
                              class="category-name"
                              href="/catalog/tatu-masinki/123/filters/"
                              ><span
                                style="background-image: url(/img/default.png)"
                              ></span>
                              тип майки</a
                            >
                            <ul class="characters">
                              <li class="characters-item">
                                <a
                                  class="category-name"
                                  href="/catalog/tatu-masinki/123/filters/"
                                  ><span
                                    style="
                                      background-image: url(/img/default.png);
                                    "
                                  ></span>
                                  Цвет</a
                                >
                              </li>
                            </ul>
                          </li>
                          <li class="types-item">
                            <a
                              class="category-name"
                              href="/catalog/tatu-masinki/123/filters/"
                              ><span
                                style="background-image: url(/img/default.png)"
                              ></span>
                              тип майки</a
                            >
                            <ul class="characters">
                              <li class="characters-item">
                                <a
                                  class="category-name"
                                  href="/catalog/tatu-masinki/123/filters/"
                                  ><span
                                    style="
                                      background-image: url(/img/default.png);
                                    "
                                  ></span>
                                  Цвет</a
                                >
                              </li>
                            </ul>
                          </li>
                        </ul>
                        <!-- <ul class="subcategory">
                          <li class="subcategory-name">
                            <a
                              href="/catalog/tatu-masinki/filters/rotornye-klassiceskie/"
                              ><span style="background-image: url()"></span>
                              Роторные классические</a
                            >
                          </li>
                          <li class="subcategory-name">
                            <a
                              href="/catalog/tatu-masinki/filters/gibridnye-klassiceskie/"
                              ><span style="background-image: url()"></span>
                              Гибридные классические</a
                            >
                          </li>
                        </ul> -->
                      </li>
                    </ul>
                  </li>
                  <li class="menu-nav__item__hide-on-MD">
                    <a href="/article/">Статьи</a>
                  </li>
                  <li><a href="/stock/">Акции</a></li>
                  <li><a href="/brands/">Бренды</a></li>
                  <li class="menu-nav__item__hide-on-MD">
                    <a href="/delivery/">Доставка</a>
                  </li>
                  <li><a href="/team/">Наша команда</a></li>
                  <li><a href="/contact/">Контакты</a></li>
                </ul>
                <div
                  :style="{ opacity: isMobileOpened ? 1 : 0 }"
                  class="menu-nav__contact"
                >
                  <div>
                    <div class="phone">
                      <a href="tel:" class="phone__number"
                        ><svg
                          class="icon"
                          width="20px"
                          height="20px"
                          fill="#363636"
                          stroke="#363636"
                        >
                          <use xlink:href="/img/svg/icons.svg#phone"></use>
                        </svg>
                      </a>
                      <div class="phone__time"></div>
                    </div>
                  </div>
                  <div>
                    <div class="phone">
                      <a href="tel:" class="phone__number"
                        ><svg
                          class="icon"
                          width="20px"
                          height="20px"
                          fill="#363636"
                          stroke="#363636"
                        >
                          <use xlink:href="/img/svg/icons.svg#phone"></use>
                        </svg>
                      </a>
                      <div class="phone__time">Только звонки</div>
                    </div>
                  </div>
                  <div class="menu-nav__contact__soc">
                    <div>Мы в соцсетях</div>
                    <a target="_blank" href="https://t.me/tattoofeel_ru"
                      ><svg class="icon" width="23px" height="23px" fill="#fff">
                        <use xlink:href="/img/svg/icons.svg#telega"></use>
                      </svg> </a
                    ><a target="_blank" href="https://t.me/tattoofeel_ru"
                      ><svg class="icon" width="23px" height="23px" fill="#fff">
                        <use xlink:href="/img/svg/icons.svg#vk"></use>
                      </svg>
                    </a>
                  </div>
                  <div
                    class="footer__pay__img"
                    style="
                      margin: 25px 0 0 40px;
                      filter: grayscale(100%);
                      mix-blend-mode: difference;
                    "
                  >
                    <a href="/pay-card/" class="img-master-visa"></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <span @click="toogleMobile" :class="{ bg__mobile: isMobileOpened }" class="mob-bg"></span>
  </div>
</template>
<script setup>
import { computed, ref } from "vue";
import { storeToRefs } from "pinia";

import { useCartStore } from "@/store/cart";
import { useMenuStore } from "@/store/menu";
import { useModalStore } from "@/store/modal";

const { getTotalCount, getTotalPrice } = storeToRefs(useCartStore());
const { isMenuOpened, isMobileOpened } = storeToRefs(useMenuStore());

const { showLoginModal } = useModalStore();
const { showMobile, hideMobile } = useMenuStore();

const isOpenedList = ref(false)

function toogleMobile() {
  if (isMobileOpened.value) {
    hideMobile();
    document.body.classList.remove("-open-mob");
  } else {
    showMobile();
    document.body.classList.add("-open-mob");
  }
}
</script>
<style lang="scss" scoped>
.container {
  max-width: calc(1440px + 32px);
  padding-left: 16px;
  padding-right: 16px;
  margin: 0 auto;
}

.toogle__menu {
  display: none;
}

@media (max-width: 422px) {
  .toogle__menu {
    display: flex;
  }
}

.menu-nav {
  &__list {
  }

  &__list__sub {
    min-width: 200px;
    max-width: 300px;

    .category {
      &:hover {
        .types {
          display: block;
        }
      }
    }

    .types {
      display: none;
    }

    .types-item {
      &:hover {
        .characters {
          display: block;
        }
      }
    }

    .characters {
      margin-left: 20px;
      display: none;
    }
    .category-name {
      // word-wrap: break-word;
      // word-break: break-all;
      white-space: normal;
      transition: 0.3s;

      &:hover {
        background-color: $yellow;
      }
    }
  }
}
</style>
