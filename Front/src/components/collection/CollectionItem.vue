<template>
  <li class="collection-item">
    <slot />
  </li>
</template>

<script>
export default {
  name: "CollectionItem",
  inject: ["CollectionInstance"],
};
</script>

<style scoped></style>
