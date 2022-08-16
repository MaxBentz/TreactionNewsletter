<template>
  <form
    :id="'newsletter-input-form_' + _uid"
    ref="newsletterForm"
    method="post"
    @submit.prevent="validateData"
  >
    <div class="row">
      <div
        v-if="showNameInputs"
        class="col-6"
      >
        <div
          class="input-unit"
          data-validate="!regex"
        >
          <label :for="'first-name-input_' + _uid">{{ $translate("Ceres::Template.newsletterFirstName") }}</label>
          <input
            :id="'first-name-input_' + _uid"
            v-model="firstName"
            type="text"
            data-validate-ref="/[.:\/\d]/g"
            data-testing="nl-first-name"
          >
        </div>
      </div>
      <div
        v-if="showNameInputs"
        class="col-6 pl-0"
      >
        <div
          class="input-unit"
          data-validate="!regex"
        >
          <label :for="'last-name-input_' + _uid">{{ $translate("Ceres::Template.newsletterLastName") }}</label>
          <input
            :id="'last-name-input_' + _uid"
            v-model="lastName"
            type="text"
            data-validate-ref="/[.:\/\d]/g"
            data-testing="nl-last-name"
          >
        </div>
      </div>
      <div class="col-12">
        <div class="input-group">
          <div
            class="input-unit"
            data-validate="mail"
          >
            <label :for="'email-input-id_' + _uid">{{ $translate("Ceres::Template.newsletterEmail") }} {{ $translate("Ceres::Template.newsletterIsRequiredFootnote") }}</label>
            <input
              :id="'email-input-id_' + _uid"
              v-model="email"
              type="email"
              autocomplete="email"
              data-testing="nl-mail"
              @focus="loadRecaptcha = true"
            >
          </div>
          <input
            v-model="honeypot"
            autocomplete="none"
            class="honey"
            type="text"
            name="username"
            tabindex="-1"
          >
        </div>
      </div>
      <div
        v-if="showPrivacyPolicyCheckbox"
        class="col-12"
      >
        <div
          class="form-check small"
          data-validate
        >
          <input
            :id="'privacy-policy-accept-id_' + _uid"
            v-model="privacyPolicyValue"
            type="checkbox"
            class="form-check-input"
            name="privacy-policy-accept"
            data-testing="nl-policy"
          >
          <label
            :for="'privacy-policy-accept-id_' + _uid"
            class="form-check-label"
            v-html="privacyPolicyText"
          />
        </div>
      </div>
      <div class="col-12 mt-3">
        <div class="input-group-btn">
          <button
            type="button"
            class="btn btn-block btn-primary btn-appearance"
            :disabled="isDisabled"
            data-testing="nl-send"
            @click="validateData"
          >
            <icon
              icon="paper-plane-o"
              :loading="isDisabled"
            />
            {{ $translate("Ceres::Template.newsletterSubscribeButtonLabel") }}
          </button>
        </div>
      </div>
      <div class="col-12 text-right small mt-2">
        {{ $translate("Ceres::Template.newsletterIsRequiredFootnote") }} {{ $translate("Ceres::Template.newsletterIsRequired") }}
      </div>
    </div>
    <recaptcha v-if="!!$ceres.config.global.googleRecaptchaApiKey && loadRecaptcha" />
  </form>
</template>
<script>
import ValidationService from "../../../../../../../plugin-ceres/resources/js/src/app/services/ValidationService"
import { executeReCaptcha } from "../../../../../../../plugin-ceres/resources/js/src/app/helper/executeReCaptcha"
export default {
    props: {
        showNameInputs:
            {
                type: Boolean,
                default: false
            },
        showPrivacyPolicyCheckbox:
            {
                type: Boolean,
                default: true
            },
        emailFolder:
            {
                type: Number,
                default: 0
            },
        treactionUuid:{
            type: text,
            default :""
        },
        treactionApikey:{
            type: text,
            default :""
        },
        treactionAccountNumber:{
            type: text,
            default :""
        }
    },
    data ()
    {
        return {
            firstName: "",
            lastName: "",
            email: "",
            isDisabled: false,
            privacyPolicyValue: false,
            honeypot: "",
            loadRecaptcha: false
        }
    },
    computed:
        {
            privacyPolicyText ()
            {
                const link = "<a href=\"" + App.urls.privacyPolicy + "\" target=\"_blank\"><span class=\"text-primary text-appearance\">" +
                    this.$translate("Ceres::Template.checkoutPrivacyPolicy", { hyphen: "&shy;" }) +
                    "</span></a>"

                return this.$translate("Ceres::Template.newsletterAcceptPrivacyPolicy", { policy: link }) + this.$translate("Ceres::Template.newsletterIsRequiredFootnote")
            }
        },
    methods: {
        validateData ()
        {
            this.isDisabled = true
            ValidationService.validate($("#newsletter-input-form_" + this._uid)).done(() =>
            {
                this.save()
            }).fail(invalidFields =>
            {
                ValidationService.markInvalidFields(invalidFields, "error")
                invalidFields.filter(field =>
                {
                    return field.dataset.validate !== null
                }).map((field) =>
                {
                    return {
                        type: field.dataset.validate,
                        name: field.innerText
                    }
                }).forEach((field) =>
                {
                    if (field.type === "!regex")
                    {
                        window.CeresNotification.error(this.$translate("Ceres::Template.newsletterNotAllowedCharacters", { name: field.name }))
                    }
                })
                this.isDisabled = false
            })
        },
        save ()
        {
            const recaptchaEl = this.$el.querySelector("[data-recaptcha]")

            if (App.config.global.googleRecaptchaApiKey && (!window.grecaptcha || !recaptchaEl))
            {
                window.CeresNotification.error(this.$translate("Ceres::Template.newsletterAcceptRecaptchaCookie"))
                this.isDisabled = false
                return
            }
            executeReCaptcha(this.$el).then((recaptchaToken) =>
            {
                if (this.honeypot !== "")
                {
                    window.CeresNotification.warn(
                        this.$translate("Ceres::Template.newsletterHoneypotWarning")
                    )
                    this.resetInputs()
                }
                else
                {
                    const formData = {
                        // @customer - This is the base configuration
                        base: {
                            account_number: "1241",
                            apikey: "c14c85f6-ee13-11ec-9aba-c43772f7ef7d",
                            // @internal - unique object identifier
                            uuid: "27558bd7-ee17-11ec-9aba-c43772f7ef7d"
                        },
                        // @customer - Lead Information
                        contact: {
                            standard: [
                                {
                                    field_name: "email",
                                    field_value: this.email,
                                    datatype: "Email",
                                    regex: "^[_a-zA-Z0-9-]+(.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(.[a-zA-Z0-9-]+)*(.[a-z]{2,9})$",
                                    required: "1"
                                }
                            ],
                            custom: []
                        }
                    }
                    const formJson = JSON.stringify(formData)
                    const formbtoa = btoa(formJson)

                    $.ajax({
                        url: "https://api-in-one.net/v2.0/webhook/receive",
                        type: "POST",
                        async: false,
                        timeout: 0,
                        header: {
                            "Access-Control-Allow-Origin": "*",
                            "Access-Control-Allow-Methods": "PUT, GET, POST, DELETE",
                            "Access-Control-Allow-Headers": "access-control-allow-headers,access-control-allow-methods,access-control-allow-origin,access-control-max-age,content-type,bearer",
                            "Access-Control-Max-Age": "86400"
                        },
                        data: formbtoa
                    }).done(result => this.successNotification()).done(result => this.resetInputs()).fail(result => this.errorNotification()).always(result => this.isDisabled = false).always(this.resetRecaptcha())
                }
            })
        },
        resetInputs ()
        {
            this.firstName = ""
            this.lastName = ""
            this.email = ""
            this.privacyPolicyValue = false
        },
        successNotification ()
        {
            window.CeresNotification.success(this.$translate("Ceres::Template.newsletterSuccessMessage"))
        },
        errorNotification ()
        {
            window.CeresNotification.error(this.$translate("Ceres::Template.newsletterErrorMessage"))
        },
        resetRecaptcha ()
        {
            if (App.config.global.googleRecaptchaVersion === 2 && window.grecaptcha)
            {
                const recaptchaId = this.$el.querySelector("[data-recaptcha]")

                window.grecaptcha.reset(recaptchaId)
            }
        }
    }
}
</script>
