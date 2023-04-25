<template>
  <notifications :duration="-1" position="bottom center">
    <template slot="body" slot-scope="props">
      <div class="vue-notification-template vue-notification">
        <p class="title">{{ props.item.title }}</p>
        <button class="close-btn" @click="props.close">
          <i class="fa fa-fw fa-close"></i>
        </button>
        <div v-html="props.item.text" class="notification-content" />
        <button :disabled="isLoading" @click="toCheckout" class="btn btn-success btn-block btn-sm">Till kassan</button>
      </div>
    </template>
  </notifications>
</template>

<script>
export default {
  data: () => ({
    isLoading: false,
  }),
  beforeDestroy() {
    this.isLoading = false;
  },
  methods: {
    toCheckout() {
      this.isLoading = true;
      this.$emit('toCheckout');
    },
  },
}
</script>

<style lang="scss" scoped>
.vue-notification {
  position: relative;
  padding: 8px;
  background: #fff;
  color: #1a1a1a;
  border-left: 5px solid #187765;
  font-size: 14px;
  font-family: 'Roboto';
  line-height: 22px;
  text-align: center;

  .title {
    margin: 0;
    font-size: 14px;
    text-align: center;
    font-family: 'Roboto';
    font-weight: 700;
    line-height: 22px;
  }

  .close-btn {
    position: absolute;
    top: 0;
    right: 0;
    background: transparent;
    border: none;
    color: #1a1a1a;
    padding: 2px;
  }

  /deep/ {
    .price {
      font-weight: 700;
    }
  }
}
</style>