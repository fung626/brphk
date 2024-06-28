<template>
    <CFooter :fixed="false">
        <div>
            <span class="ml-1"
                >&copy; {{ new Date().getFullYear() }} Best Rise Property.</span
            >
        </div>
        <div class="ml-auto">
            <span class="mr-1">Powered by</span>
            <a href="／" target="_blank">Best Rise Property</a>
        </div>
    </CFooter>
</template>

<script>
export default {
    name: "TheFooter"
};
</script>
