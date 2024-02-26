<script>
/*
* Navigate to
*/
export default {
  props: {
    routeName: { type: String },
  }
};
</script>
<template>
    <div>
        <router-link :to="{ name: routeName }" class="btn btn-primary">
            {{ routeName.replaceAll('-', ' ') }}
            <i class="mdi mdi-plus mr-1"></i>
        </router-link>
    </div>
</template>