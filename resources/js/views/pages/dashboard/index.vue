<script>
import Layout from "../../layout/mainLayout";
import PageHeader from "@/components/page-header";
import ICountUp from "vue-countup-v2";
import appConfig from "@/app.config";
/**
 * Dashboard component
 */
export default {
  name: "home",
  page: {
    title: "Dashboard",
    meta: [
      {
        name: "description",
      },
    ],
  },
  components: {
    Layout,
    PageHeader,
    ICountUp,
  },
  data() {
    return {
      title: "Dashboard",
      
      options: {
        useEasing: true,
        useGrouping: true,
      },
    };
  },
  methods: {
    fetchData() {
      
    },
  },
  mounted() {

  },
};
</script>

<template>
  <Layout>
    <PageHeader :title="title" />
    
  </Layout>
</template>
<style scoped>
div.unit {
  display: inline-flex;
  position: absolute;
  right: 2%;
  font-size: 1rem;
  bottom: 20%;
  color: #5664d2;
}

hr {
  width: 98%;
}

.filter_order {
  margin-bottom: 1rem;
}

.filter_order label,
label.col-form-label {
  width: 30%;
  display: inline-block;
}

select {
  display: inline-block;
  width: 68%;
  height: 38px;
}
input.form-control {
  display: inline-block;
  width: 98%;
}
</style>
