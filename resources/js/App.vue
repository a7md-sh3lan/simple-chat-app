<script>
import appConfig from "@/app.config";

export default {
  name: "app",
  page: {
    // All subcomponent titles will be injected into this template.
    titleTemplate(title) {
      title = typeof title === "function" ? title(this.$store) : title;
      return title ? `${title} | ${appConfig.pageTitle}` : appConfig.pageTitle;
    }
  }
};
</script>
<template>
  <div id="app">
    <router-view />
  </div>
</template>