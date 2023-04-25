<template>
	<div class="no-results">
		<h3 class="no-results-title" v-if="title">{{ title }}</h3>
		<slot name="description"></slot>
	</div>
</template>

<script type="text/babel">
	export default {
		props: ['title', 'description']
	}
</script>

<style lang="scss" type="text/scss">
</style>
