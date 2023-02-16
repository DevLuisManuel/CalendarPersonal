<template>
  <ul class="collection">
    <slot />
  </ul>
</template>

<script>
export default {
  // eslint-disable-next-line vue/multi-word-component-names
  name: "Collection",
  provide() {
    return {
      CollectionInstance: this,
    };
  },
};
</script>

<style scoped>
.collection {
  margin: 0.5rem 0 1rem 0;
  border: 1px solid #e0e0e0;
  border-radius: 2px;
  overflow: hidden;
  position: relative;
}

.collection .collection-item {
  background-color: #fff;
  line-height: 1.5rem;
  padding: 10px 20px;
  margin: 0;
  border-bottom: 1px solid #e0e0e0;
}

ul:not(.browser-default) {
  padding-left: 0;
  list-style-type: none;
}

ul:not(.browser-default) > li {
  list-style-type: none;
}
</style>
