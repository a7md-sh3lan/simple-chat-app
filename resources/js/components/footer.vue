<script>
export default {
  data() {
    return {
      currentYear: new Date().getFullYear(),
    };
  },
};
</script>
<template>
  <footer class="footer">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12">
          <div class="text-sm d-none d-sm-block">
            Copyright © Ahmad Sha'lan {{ currentYear }}, All Rights Reserved.
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>
