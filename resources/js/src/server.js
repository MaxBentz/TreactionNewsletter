function beforeCreate (context)
{
    Vue.component("TreactionNewsletterInput", () => import("./app/components/newsletter/TreactionNewsletterInput.vue"))
}

function beforeRender (vueApp)
{
}

export { beforeCreate, beforeRender }
