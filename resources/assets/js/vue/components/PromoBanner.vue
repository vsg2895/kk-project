<style>
  .slide-item{
    width: 100%;
    display: flex;
    justify-content: center;
    min-height: 80px;
    align-items: center;

  }
  .slide-item span{
    font-size: 22px;
    font-style: italic;
  }
  .slide-item img{
    max-height: 50px;
  }
</style>

<template>
    <slick :options="bannerSlickOptions">
      <div class="slide">
        <div class="slide-item">
<!--          <span>Just nu flera bra erbjudanden på-->
          <span>Samla <b>BONUS</b> och förmåner på samtliga köp
<!--            <span>God Jul önskar vi på Körkortsjakten - Få 50:- rabatt med koden-->
<!--              <b class="copy-text" style="cursor: pointer" @click="copyToClipboard">JUL50</b>-->
<!--            <a href="/musikhjalpen"><b style="cursor: pointer">Klicka Här!</b></a>-->
          </span>
        </div>
      </div>
      <div class="slide">
        <div class="slide-item">
<!--          <img :src="'/images/klarna-logo-shopping.jpg'" alt="Klarna logo">-->
          <img :src="'/images/klarna-shoppa-black.jpg'" alt="Klarna logo">
        </div>
      </div>
    </slick>
</template>

<script>
import Slick from 'vue-slick';

export default {
  components: {
    Slick,
  },
  data() {
    return {
      bannerSlickOptions: {
        slidesToShow: 1,
        slidesToScroll: 1,
        infinite: false,
        arrows: false,
        autoplay: true,
        autoplaySpeed: 1500000,
        speed: 1300
      },
    }
  },
  methods: {
    copyToClipboard() {
      let copyElem = $('.copy-text');
      copyElem.attr('title', 'Copied to clipboard');
      navigator.clipboard.writeText('BW50');
    }
  }
}
</script>