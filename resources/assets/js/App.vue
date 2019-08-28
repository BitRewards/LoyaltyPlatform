<template>
  <div>
    <CashierApp :current-client='$root.currentClient'></CashierApp>
  </div>
</template>

<script>
import CashierApp from './components/CashierApp.vue'

export default {
  components: {
    CashierApp
  }
}
</script>

<style>

</style>
