<template>
  <div class="container mt-5">
    <div class="text-center">
      <h1 class="display-1">404</h1>
      <p class="fs-3">Trang không tìm thấy</p>
      <p class="lead">Xin lỗi, trang bạn đang tìm không tồn tại.</p>
      <router-link to="/" class="btn btn-primary">Về Trang Chủ</router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NotFound'
};
</script>
