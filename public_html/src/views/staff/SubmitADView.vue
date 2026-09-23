<template>
      <main class="twg-view-wrapper">
        <div class="main-content-container">
          <div class="form-header">
            <h1 class="form-main-title">Submit Activity Design</h1>
            <p class="form-description">Fill out the activity design form below. All fields marked with * are required.</p>
          </div>

          <div class="form-container-box">
            <form @submit.prevent="submitActivityDesign" class="form-main-layout">
              <div class="form-grid-main">
                <div class="form-column-left">
                  <div class="form-sub-grid">
                    <div class="input-group">
                      <label class="form-label">Form Type *</label>
                      <select 
                        v-model="form.form_type" 
                        required 
                        class="custom-input-field select-arrow-fix"
                      >
                        <option value="" disabled class="dark-option">Select form type...</option>
                        <option 
                          v-for="ft in formTypes" 
                          :key="ft.id" 
                          :value="ft.id" 
                          class="dark-option"
                        >
                          {{ ft.name }}
                        </option>
                      </select>
                    </div>

                    <div class="input-group">
                      <label class="form-label">Activity Classification *</label>
                      <select
                        v-model="form.activity_classification_id"
                        required
                        class="custom-input-field select-arrow-fix"
                      >
                        <option value="" disabled class="dark-option">Select Classification</option>
                        <option
                          v-for="classification in ActClassification"
                          :key="classification.id"
                          :value="classification.id"
                          class="dark-option"
                        >
                          {{ classification.classification_name }}
                        </option>
                      </select>
                    </div>
                  </div>

                  <div class="input-group">
                    <label class="form-label">Activity Title *</label>
                    <textarea 
                      v-model="form.activity_title" 
                      required 
                      rows="4" 
                      class="custom-input-field textarea-no-resize"
                      placeholder="Enter the complete title of the activity"
                    ></textarea>
                  </div>
                </div>

                <div class="form-column-right">
                  <div class="input-group">
                    <label class="form-label">Gender Issue / GAD Mandate *</label>
                    <CustomSelect 
                      v-model="form.gad_mandate_id" 
                      :options="GADMandates"
                      placeholder="Select GAD mandate..."
                      search-placeholder="Search GAD mandate..."
                      no-results-text="No matching GAD mandates"
                    />
                  </div>

                  <div class="input-group">
                    <label class="form-label">Cause of Gender Issue *</label>
                    <CustomSelect 
                      v-model="form.gender_issue_id" 
                      :options="genderIssueOptions"
                      :disabled="!form.gad_mandate_id"
                      :placeholder="!form.gad_mandate_id ? 'Select a mandate first...' : 'Select cause of gender issue...'"
                      search-placeholder="Search cause of gender issue..."
                      no-results-text="No matching causes"
                    />
                    <input v-if="form.gender_issue_id && form.gender_issue_id === 'Other'" 
                          v-model="customGenderIssue" 
                          type="text" 
                          placeholder="Enter new gender issue..." 
                          class="custom-input-field" 
                          style="margin-top: 10px;" />
                  </div>
                </div>
              </div>

              <!-- Visual Section Divider -->
              <div class="form-section-divider">
                <div class="form-section-divider-line"></div>
              </div>


                                          <!-- SECTION 2: Venues, Schedules & Budgetary Requirements -->
              <div class="venues-full-wrapper mb-6">
                <div class="flex justify-between items-center mb-4 flex-wrap gap-4">
                  <div>
                    <label class="form-label !mb-0 flex items-center gap-2 text-xl font-extrabold text-white">
                      <span class="material-symbols-outlined text-[#c084fc] text-2xl">domain_add</span>
                      Venues, Schedules & Budgetary Requirements *
                    </label>
                    <p class="text-sm text-slate-300 mt-1">Configure each campus/venue with its specific schedule dates and participant count. Meals and day counts sync automatically into the budget.</p>
                  </div>
                  <div class="text-sm font-bold text-purple-200 bg-purple-500/20 border border-purple-500/40 px-3.5 py-2 rounded-xl flex items-center gap-2">
                    <span class="material-symbols-outlined text-base text-purple-300">domain</span>
                    {{ venuesList.length }} {{ venuesList.length === 1 ? 'Venue Configured' : 'Venues Configured' }}
                  </div>
                </div>

                <!-- Venue Cards Loop -->
                <div v-for="(vCard, vIdx) in venuesList" :key="vCard.id" class="venue-budget-card mb-6">
                  <!-- Venue Card Header -->
                  <div class="venue-card-header">
                    <div class="venue-header-left">
                      <span class="venue-index-badge">Venue {{ vIdx + 1 }}</span>
                      <span class="font-extrabold text-white text-lg">{{ getVenueDisplayName(vCard) }}</span>
                      <span class="text-xs px-3 py-1 rounded-full font-bold uppercase tracking-wider" :class="vCard.is_inside_bsu ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'bg-blue-500/20 text-blue-300 border border-blue-500/40'">
                        {{ vCard.is_inside_bsu ? 'Inside BSU' : 'Outside BSU' }}
                      </span>
                    </div>
                    <div class="venue-header-right">
                      <div class="venue-subtotal-badge">
                        Venue Subtotal: <span class="text-white font-mono font-extrabold">₱{{ formatNum(computeVenueTotal(vCard)) }}</span>
                      </div>
                      <button 
                        type="button" 
                        v-if="venuesList.length > 1" 
                        @click="removeVenue(vIdx)" 
                        class="venue-remove-btn"
                        title="Remove Venue"
                      >
                        <span class="material-symbols-outlined text-base">delete</span> Remove Venue
                      </button>
                    </div>
                  </div>

                  <!-- 1. Venue Details Grid -->
                  <div class="venue-details-grid">
                    <div>
                      <label class="section-field-label">Location Type *</label>
                      <div class="toggle-container" style="display: flex; gap: 1.25rem; align-items: center; height: 44px;">
                        <label style="color: #ffffff; font-size: 14px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                          <input type="radio" :value="true" v-model="vCard.is_inside_bsu" @change="handleVenueLocationToggle(vCard, true)" style="accent-color: #b979cc; width: 16px; height: 16px;" /> Inside BSU
                        </label>
                        <label style="color: #ffffff; font-size: 14px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                          <input type="radio" :value="false" v-model="vCard.is_inside_bsu" @change="handleVenueLocationToggle(vCard, false)" style="accent-color: #b979cc; width: 16px; height: 16px;" /> Outside BSU
                        </label>
                      </div>
                    </div>

                    <div>
                      <label class="section-field-label">Select Venue / Campus *</label>
                      <select 
                        v-model="vCard.venue_id" 
                        required 
                        class="custom-input-field select-arrow-fix venue-input-large"
                      >
                        <option value="" disabled class="dark-option">Select venue...</option>
                        <option 
                          v-for="v in getFilteredVenuesForCard(vCard)" 
                          :key="v.venue_id" 
                          :value="v.venue_id" 
                          class="dark-option"
                        >
                          {{ v.venue_name }}
                        </option>
                        <option value="Other" class="dark-option">Other (Specify below)</option>
                      </select>
                      <input 
                        v-if="vCard.venue_id === 'Other'" 
                        type="text" 
                        v-model="vCard.custom_venue" 
                        required 
                        placeholder="Enter specific campus or venue name..." 
                        class="custom-input-field venue-input-large mt-2" 
                      />
                    </div>

                    <div style="min-width: 200px;">
                      <div class="flex items-center justify-between mb-1.5">
                        <label class="section-field-label !mb-0">Peak Venue Pax *</label>
                        <span class="text-[11px] text-purple-300 font-semibold">(Max meal pax)</span>
                      </div>
                      <div class="custom-input-field venue-input-large flex items-center justify-between font-mono font-bold text-white bg-black/40 border border-purple-500/30">
                        <span class="flex items-center gap-1.5">
                          <span class="material-symbols-outlined text-sm text-[#c084fc]">groups</span>
                          <span class="text-xs uppercase text-slate-300">Peak:</span>
                        </span>
                        <span class="text-base font-extrabold text-purple-200">
                          {{ getVenueMaxPax(vCard) }} <span class="text-xs font-normal text-slate-300">pax</span>
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- 2. Schedules & Daily Meals for this Venue -->
                  <div class="venue-schedule-box mt-4 p-5 rounded-2xl border border-white/10 bg-black/35">
                    <div class="flex justify-between items-center mb-4 flex-wrap gap-3">
                      <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg text-[#c084fc]">calendar_month</span>
                        <span class="text-sm font-extrabold text-white uppercase tracking-wider">Schedule & Daily Meals for this Venue</span>
                      </div>
                      <!-- Schedule mode toggle for this venue -->
                      <div class="schedule-mode-toggle-group">
                        <button 
                          type="button" 
                          @click.prevent="handleVenueScheduleTypeChange(vCard, 'staggered')" 
                          class="schedule-mode-btn"
                          :class="{ 'mode-active': vCard.schedule_type === 'staggered' }"
                        >
                          Non-Consecutive
                        </button>
                        <button 
                          type="button" 
                          @click.prevent="handleVenueScheduleTypeChange(vCard, 'continuous')" 
                          class="schedule-mode-btn"
                          :class="{ 'mode-active': vCard.schedule_type === 'continuous' }"
                        >
                          Consecutive
                        </button>
                      </div>
                    </div>

                    <!-- Consecutive Range Config (if continuous) -->
                    <div v-if="vCard.schedule_type === 'continuous'" class="mb-4 p-4 rounded-xl border border-purple-500/35 bg-purple-500/10 text-sm">
                      <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-3">
                        <div>
                          <label class="section-field-label">Start Date</label>
                          <VueDatePicker dark v-model="vCard.continuous_config.start_date" :min-date="minStartDate" :disabled-dates="isDisabledDate" model-type="yyyy-MM-dd" :enable-time-picker="false" format="MM/dd/yyyy" auto-apply required input-class-name="custom-input-field dp-custom-transparent" :max-date="maxDateLimit" @update:model-value="generateConsecutiveSchedulesForVenue(vCard)">
                            <template #dp-input="{ value }">
                              <input type="text" :value="value ? String(value).replace(',', '').trim().split(' ')[0] : ''" class="custom-input-field dp-custom-transparent venue-input-large" readonly placeholder="Select Date" />
                            </template>
                          </VueDatePicker>
                        </div>
                        <div>
                          <label class="section-field-label">End Date</label>
                          <VueDatePicker dark v-model="vCard.continuous_config.end_date" :min-date="vCard.continuous_config.start_date || minStartDate" :disabled-dates="isDisabledDate" model-type="yyyy-MM-dd" :enable-time-picker="false" format="MM/dd/yyyy" auto-apply required input-class-name="custom-input-field dp-custom-transparent" :max-date="maxDateLimit" @update:model-value="generateConsecutiveSchedulesForVenue(vCard)">
                            <template #dp-input="{ value }">
                              <input type="text" :value="value ? String(value).replace(',', '').trim().split(' ')[0] : ''" class="custom-input-field dp-custom-transparent venue-input-large" readonly placeholder="Select Date" />
                            </template>
                          </VueDatePicker>
                        </div>
                        <div>
                          <label class="section-field-label">Time From</label>
                          <input type="time" v-model="vCard.continuous_config.start_time" min="04:00" max="20:00" required class="custom-input-field venue-input-large" style="color-scheme: dark; cursor: pointer;" @change="generateConsecutiveSchedulesForVenue(vCard)" />
                        </div>
                        <div>
                          <label class="section-field-label">Time To</label>
                          <input type="time" v-model="vCard.continuous_config.end_time" min="04:00" max="20:00" required class="custom-input-field venue-input-large" style="color-scheme: dark; cursor: pointer;" @change="generateConsecutiveSchedulesForVenue(vCard)" />
                        </div>
                      </div>
                      <div class="flex gap-4 items-center flex-wrap pt-3 border-t border-white/10">
                        <span class="text-xs uppercase font-extrabold text-[#d8b4fe]">Meals Applied Daily:</span>
                        <label class="meal-check-pill">
                          <input type="checkbox" v-model="vCard.continuous_config.meals_and_snacks.breakfast" @change="generateConsecutiveSchedulesForVenue(vCard)" style="accent-color: #b979cc; width: 16px; height: 16px;" /> Breakfast
                        </label>
                        <label class="meal-check-pill">
                          <input type="checkbox" v-model="vCard.continuous_config.meals_and_snacks.am_snack" @change="generateConsecutiveSchedulesForVenue(vCard)" style="accent-color: #b979cc; width: 16px; height: 16px;" /> AM Snack
                        </label>
                        <label class="meal-check-pill">
                          <input type="checkbox" v-model="vCard.continuous_config.meals_and_snacks.lunch" @change="generateConsecutiveSchedulesForVenue(vCard)" style="accent-color: #b979cc; width: 16px; height: 16px;" /> Lunch
                        </label>
                        <label class="meal-check-pill">
                          <input type="checkbox" v-model="vCard.continuous_config.meals_and_snacks.pm_snack" @change="generateConsecutiveSchedulesForVenue(vCard)" style="accent-color: #b979cc; width: 16px; height: 16px;" /> PM Snack
                        </label>
                        <label class="meal-check-pill">
                          <input type="checkbox" v-model="vCard.continuous_config.meals_and_snacks.dinner" @change="generateConsecutiveSchedulesForVenue(vCard)" style="accent-color: #b979cc; width: 16px; height: 16px;" /> Dinner
                        </label>
                      </div>
                    </div>

                    <!-- Individual Schedule Rows for this Venue -->
                    <div v-for="(sch, sIdx) in vCard.schedules" :key="sIdx" class="venue-schedule-row mb-3 p-4 rounded-xl border border-white/10 bg-white/[0.03] relative">
                      <div class="flex items-center gap-3 flex-wrap">
                        <div style="flex: 1.3; min-width: 170px;">
                          <label class="section-field-label">Date</label>
                          <VueDatePicker dark v-model="sch.date" :disabled="vCard.schedule_type === 'continuous'" :min-date="minStartDate" :disabled-dates="isDisabledDate" model-type="yyyy-MM-dd" :enable-time-picker="false" format="MM/dd/yyyy" auto-apply required input-class-name="custom-input-field dp-custom-transparent" :max-date="maxDateLimit">
                            <template #dp-input="{ value }">
                              <input type="text" :value="value ? String(value).replace(',', '').trim().split(' ')[0] : ''" class="custom-input-field dp-custom-transparent venue-input-large" readonly placeholder="Select Date" />
                            </template>
                          </VueDatePicker>
                        </div>
                        <div style="flex: 1; min-width: 130px;">
                          <label class="section-field-label">Start Time</label>
                          <input type="time" v-model="sch.start_time" min="04:00" max="20:00" required class="custom-input-field venue-input-large" style="color-scheme: dark; cursor: pointer;" @change="handleVenueScheduleTimeChange(vCard, sIdx)">
                        </div>
                        <div style="flex: 1; min-width: 130px;">
                          <label class="section-field-label">End Time</label>
                          <input type="time" v-model="sch.end_time" min="04:00" max="20:00" required class="custom-input-field venue-input-large" style="color-scheme: dark; cursor: pointer;" @change="handleVenueScheduleTimeChange(vCard, sIdx)">
                        </div>
                        <button 
                          type="button" 
                          v-if="vCard.schedules.length > 1 && vCard.schedule_type === 'staggered'" 
                          @click.prevent="removeScheduleFromVenue(vCard, sIdx)" 
                          class="schedule-row-remove-btn" 
                          title="Remove Date"
                        >
                          <span class="material-symbols-outlined text-base">delete</span>
                        </button>
                      </div>

                      <div class="flex gap-3 items-center flex-wrap mt-3 pt-3 border-t border-white/10 text-sm">
                        <span class="text-xs uppercase font-extrabold text-[#d8b4fe]">Meals Needed for this Date:</span>
                        <label class="meal-check-pill" :class="{ 'pill-disabled': sch.start_time && Number(sch.start_time.split(':')[0]) >= 13 }">
                          <input type="checkbox" v-model="sch.meals_and_snacks.breakfast" :disabled="sch.start_time && Number(sch.start_time.split(':')[0]) >= 13" style="accent-color: #b979cc; width: 16px; height: 16px;" /> Breakfast
                        </label>
                        <label class="meal-check-pill" :class="{ 'pill-disabled': sch.start_time && Number(sch.start_time.split(':')[0]) >= 13 }">
                          <input type="checkbox" v-model="sch.meals_and_snacks.am_snack" :disabled="sch.start_time && Number(sch.start_time.split(':')[0]) >= 13" style="accent-color: #b979cc; width: 16px; height: 16px;" /> AM Snack
                        </label>
                        <label class="meal-check-pill">
                          <input type="checkbox" v-model="sch.meals_and_snacks.lunch" style="accent-color: #b979cc; width: 16px; height: 16px;" /> Lunch
                        </label>
                        <label class="meal-check-pill" :class="{ 'pill-disabled': sch.end_time && (Number(sch.end_time.split(':')[0]) < 12 || sch.end_time === '12:00') }">
                          <input type="checkbox" v-model="sch.meals_and_snacks.pm_snack" :disabled="sch.end_time && (Number(sch.end_time.split(':')[0]) < 12 || sch.end_time === '12:00')" style="accent-color: #b979cc; width: 16px; height: 16px;" /> PM Snack
                        </label>
                        <label class="meal-check-pill" :class="{ 'pill-disabled': sch.end_time && (Number(sch.end_time.split(':')[0]) < 12 || sch.end_time === '12:00') }">
                          <input type="checkbox" v-model="sch.meals_and_snacks.dinner" :disabled="sch.end_time && (Number(sch.end_time.split(':')[0]) < 12 || sch.end_time === '12:00')" style="accent-color: #b979cc; width: 16px; height: 16px;" /> Dinner
                        </label>
                      </div>
                    </div>

                    <!-- Add Schedule Date Button -->
                    <button 
                      type="button" 
                      v-if="vCard.schedule_type === 'staggered'" 
                      @click.prevent="addScheduleToVenue(vCard)" 
                      class="btn-add-schedule-date"
                    >
                      <span class="material-symbols-outlined text-lg">add_circle</span> Add Schedule Date for {{ getVenueDisplayName(vCard) }}
                    </button>
                  </div>

                  <!-- 3. Venue Meals & Snacks Breakdown (Auto-Computed from Above Schedules!) -->
                  <div class="meals-section-wrapper mt-5">
                    <div class="flex justify-between items-center flex-wrap gap-3 mb-3">
                      <div>
                        <div class="flex items-center gap-2 font-extrabold text-white text-base">
                          <span>🍽️</span> Meals & Snacks Budget (Auto-Computed from Schedule)
                        </div>
                        <div class="text-xs text-emerald-400 font-semibold italic mt-0.5">
                          (refer to E.O No. 77 and BSU Office Memorandum 117, s. 2024)
                        </div>
                      </div>
                      <div class="flex items-center gap-2">
                        <span class="meals-subtotal-header-badge">
                          Meals Subtotal: ₱{{ formatNum(computeVenueMealsTotal(vCard)) }}
                        </span>
                      </div>
                    </div>

                    <!-- Constant Pax Tool for this Venue -->
                    <div class="constant-pax-tool-bar mb-4 p-3.5 rounded-xl bg-purple-500/10 border border-purple-500/30 flex items-center justify-between flex-wrap gap-3">
                      <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-purple-300 text-lg">group_add</span>
                        <div>
                          <div class="text-sm font-bold text-white">Constant Pax for this Venue</div>
                          <div class="text-xs text-slate-300">Quickly apply a fixed participant count to all scheduled meals below</div>
                        </div>
                      </div>
                      <div class="flex items-center gap-2">
                        <input 
                          type="number" 
                          v-model.number="vCard.constant_pax" 
                          min="1" 
                          placeholder="e.g. 50" 
                          class="custom-input-field venue-input-large !w-28 text-center font-extrabold" 
                          @keyup.enter="applyConstantPaxToVenue(vCard)"
                        />
                        <button 
                          type="button" 
                          @click="applyConstantPaxToVenue(vCard)" 
                          class="btn-apply-constant-pax"
                          title="Apply constant pax to all meals"
                        >
                          Apply to All
                        </button>
                      </div>
                    </div>

                    <!-- If meals are scheduled -->
                    <div v-if="hasAnyMealsScheduledInVenue(vCard)" class="meals-grid">
                      <template v-for="mKey in ['breakfast', 'am_snack', 'lunch', 'pm_snack', 'dinner']" :key="mKey">
                        <div v-if="getVenueMealDays(vCard, mKey) > 0" class="meal-card-item meal-active">
                          <div class="flex justify-between items-center mb-2">
                            <span class="font-extrabold text-sm text-white capitalize">{{ mKey.replace('_', ' ') }}</span>
                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-purple-500/30 text-purple-200 border border-purple-500/40 font-bold">
                              {{ getVenueMealDays(vCard, mKey) }} {{ getVenueMealDays(vCard, mKey) === 1 ? 'day' : 'days' }}
                            </span>
                          </div>
                          <div class="space-y-2 pt-1 text-sm">
                            <div class="flex items-center gap-2">
                              <span class="text-xs uppercase font-bold text-slate-300 w-12">Pax:</span>
                              <input 
                                type="number" 
                                v-model.number="vCard.meals_custom[mKey].pax" 
                                min="1" 
                                :placeholder="vCard.constant_pax ? String(vCard.constant_pax) : 'Pax'" 
                                class="custom-input-field meal-field-input font-bold" 
                              />
                            </div>
                            <div class="flex items-center gap-2">
                              <span class="text-xs uppercase font-bold text-slate-300 w-12">Rate:</span>
                              <div class="flex items-center gap-1 flex-1">
                                <span class="text-slate-300 font-bold text-sm">₱</span>
                                <input 
                                  type="number" 
                                  v-model.number="vCard.meals_custom[mKey].cost" 
                                  min="0" 
                                  step="0.01" 
                                  class="custom-input-field meal-field-input" 
                                />
                              </div>
                            </div>
                            <div class="text-right text-sm font-mono font-extrabold text-purple-200 pt-1.5 border-t border-white/10">
                              = ₱{{ formatNum(computeVenueMealSubtotal(vCard, mKey)) }}
                            </div>
                          </div>
                        </div>
                      </template>
                    </div>
                    <div v-else class="text-sm text-slate-300 italic p-4 rounded-xl bg-black/30 border border-white/10">
                      💡 No meals selected for this venue yet. Check the meals needed on the schedule dates above to auto-compute the budget.
                    </div>
                  </div>

                  <!-- 4. Venue-Specific Additional Expenses (Optional) -->
                  <div class="mt-5">
                    <div class="flex items-center justify-between flex-wrap gap-2 mb-2">
                      <label class="text-sm font-extrabold text-white uppercase tracking-wider block">
                        Venue-Specific Facilities & Rental (Optional)
                      </label>
                      <span class="text-xs text-slate-300">Click if this specific venue charges rental or facility costs:</span>
                    </div>

                    <div class="quick-add-toolbar">
                      <button 
                        type="button" 
                        @click="toggleBudgetItem(vCard, 'function_room')" 
                        class="quick-add-pill" 
                        :class="{ 'pill-active': vCard.active_items.function_room }"
                      >
                        <span class="pill-icon">{{ vCard.active_items.function_room ? '✓' : '+' }}</span> Function Room / Venue
                      </button>
                      <button 
                        type="button" 
                        @click="toggleBudgetItem(vCard, 'accommodation')" 
                        class="quick-add-pill" 
                        :class="{ 'pill-active': vCard.active_items.accommodation }"
                      >
                        <span class="pill-icon">{{ vCard.active_items.accommodation ? '✓' : '+' }}</span> Accommodation
                      </button>
                      <button 
                        type="button" 
                        @click="toggleBudgetItem(vCard, 'equipment_rental')" 
                        class="quick-add-pill" 
                        :class="{ 'pill-active': vCard.active_items.equipment_rental }"
                      >
                        <span class="pill-icon">{{ vCard.active_items.equipment_rental ? '✓' : '+' }}</span> Equipment Rental
                      </button>
                      <button 
                        type="button" 
                        @click="toggleBudgetItem(vCard, 'transportation')" 
                        class="quick-add-pill" 
                        :class="{ 'pill-active': vCard.active_items.transportation }"
                      >
                        <span class="pill-icon">{{ vCard.active_items.transportation ? '✓' : '+' }}</span> Venue Transportation
                      </button>
                      <button 
                        type="button" 
                        @click="toggleBudgetItem(vCard, 'others')" 
                        class="quick-add-pill" 
                        :class="{ 'pill-active': vCard.active_items.others }"
                      >
                        <span class="pill-icon">{{ vCard.active_items.others ? '✓' : '+' }}</span> Venue Others
                      </button>
                    </div>

                    <!-- Active Venue-Specific Cards -->
                    <!-- Function Room / Venue -->
                    <div v-if="vCard.active_items.function_room" class="active-item-card">
                      <div class="flex justify-between items-center mb-3">
                        <div>
                          <span class="font-extrabold text-sm text-white">🏨 Function Room / Venue Rental for {{ getVenueDisplayName(vCard) }}</span>
                          <span class="text-xs text-slate-300 ml-2">(Leave 0 for free/attribution)</span>
                        </div>
                        <button type="button" @click="toggleBudgetItem(vCard, 'function_room')" class="item-remove-badge" title="Remove">
                          <span>×</span> Remove
                        </button>
                      </div>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" v-model="vCard.function_room.description" placeholder="Description / Hall name" class="custom-input-field venue-input-large" />
                        <div class="flex items-center gap-2">
                          <span class="text-slate-300 font-bold text-sm">₱</span>
                          <input type="number" v-model.number="vCard.function_room.cost" min="0" step="0.01" placeholder="0.00" class="custom-input-field venue-input-large flex-1 font-bold" />
                        </div>
                      </div>
                    </div>

                    <!-- Accommodation -->
                    <div v-if="vCard.active_items.accommodation" class="active-item-card">
                      <div class="flex justify-between items-center mb-3">
                        <span class="font-extrabold text-sm text-white">🛏️ Accommodation at {{ getVenueDisplayName(vCard) }}</span>
                        <button type="button" @click="toggleBudgetItem(vCard, 'accommodation')" class="item-remove-badge" title="Remove">
                          <span>×</span> Remove
                        </button>
                      </div>
                      <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                        <div>
                          <label class="section-field-label">Pax</label>
                          <input type="number" v-model.number="vCard.accommodation.pax" min="1" placeholder="Pax" class="custom-input-field venue-input-large" />
                        </div>
                        <div>
                          <label class="section-field-label">Nights</label>
                          <input type="number" v-model.number="vCard.accommodation.nights" min="1" placeholder="1" class="custom-input-field venue-input-large" />
                        </div>
                        <div>
                          <label class="section-field-label">Rate/Night (₱)</label>
                          <input type="number" v-model.number="vCard.accommodation.rate" min="0" step="0.01" placeholder="Rate" class="custom-input-field venue-input-large" />
                        </div>
                        <div>
                          <label class="section-field-label">Total (₱)</label>
                          <div class="custom-input-field venue-input-large flex items-center font-mono font-extrabold text-purple-200">
                            ₱{{ formatNum((Number(vCard.accommodation.pax) || 0) * (Number(vCard.accommodation.nights) || 0) * (Number(vCard.accommodation.rate) || 0)) }}
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Equipment Rental -->
                    <div v-if="vCard.active_items.equipment_rental" class="active-item-card">
                      <div class="flex justify-between items-center mb-3">
                        <span class="font-extrabold text-sm text-white">🎤 Equipment Rental for {{ getVenueDisplayName(vCard) }}</span>
                        <button type="button" @click="toggleBudgetItem(vCard, 'equipment_rental')" class="item-remove-badge" title="Remove">
                          <span>×</span> Remove
                        </button>
                      </div>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" v-model="vCard.equipment_rental.description" placeholder="Equipment details (e.g. Sounds & lights)" class="custom-input-field venue-input-large" />
                        <div class="flex items-center gap-2">
                          <span class="text-slate-300 font-bold text-sm">₱</span>
                          <input type="number" v-model.number="vCard.equipment_rental.cost" min="0" step="0.01" placeholder="0.00" class="custom-input-field venue-input-large flex-1 font-bold" />
                        </div>
                      </div>
                    </div>

                    <!-- Transportation for Venue -->
                    <div v-if="vCard.active_items.transportation" class="active-item-card">
                      <div class="flex justify-between items-center mb-3">
                        <span class="font-extrabold text-sm text-white">🚐 Venue Transportation for {{ getVenueDisplayName(vCard) }}</span>
                        <button type="button" @click="toggleBudgetItem(vCard, 'transportation')" class="item-remove-badge" title="Remove">
                          <span>×</span> Remove
                        </button>
                      </div>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" v-model="vCard.transportation.description" placeholder="Route / Vehicle details" class="custom-input-field venue-input-large" />
                        <div>
                          <div class="flex items-center gap-2">
                            <span class="text-slate-300 font-bold text-sm">₱</span>
                            <input type="number" v-model.number="vCard.transportation.total" min="0" step="0.01" placeholder="0.00" class="custom-input-field venue-input-large flex-1 font-bold" />
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Others for Venue -->
                    <div v-if="vCard.active_items.others" class="active-item-card">
                      <div class="flex justify-between items-center mb-3">
                        <span class="font-extrabold text-sm text-white">📋 Other Expenses for {{ getVenueDisplayName(vCard) }}</span>
                        <button type="button" @click="toggleBudgetItem(vCard, 'others')" class="item-remove-badge" title="Remove">
                          <span>×</span> Remove
                        </button>
                      </div>
                      <div class="space-y-3">
                        <div v-for="(o, oIdx) in vCard.others" :key="oIdx" class="flex items-center gap-3">
                          <input type="text" v-model="o.name" placeholder="Item name" class="custom-input-field venue-input-large flex-1" />
                          <div class="flex items-center gap-2 w-44">
                            <span class="text-slate-300 font-bold text-sm">₱</span>
                            <input type="number" v-model.number="o.amount" min="0" step="0.01" placeholder="0.00" class="custom-input-field venue-input-large flex-1 font-bold" />
                          </div>
                          <button type="button" @click="removeOtherItemFromVenue(vCard, oIdx)" class="item-line-remove-btn" title="Remove line item">
                            <span class="material-symbols-outlined text-base">close</span>
                          </button>
                        </div>
                        <button type="button" @click="addOtherItemToVenue(vCard)" class="btn-add-line-item">
                          <span class="material-symbols-outlined text-base">add</span> Add Venue Line Item
                        </button>
                      </div>
                    </div>

                  </div>
                </div>

                <!-- Add Another Venue Button -->
                <button 
                  type="button" 
                  @click.prevent="addVenue" 
                  class="btn-add-venue"
                >
                  <span class="material-symbols-outlined text-xl">add_location_alt</span>
                  + Add Another Venue / Campus
                </button>
              </div>

              <!-- Visual Section Divider -->
              <div class="form-section-divider">
                <div class="form-section-divider-line"></div>
              </div>

              <!-- SECTION 3: Activity-Wide / General Budget Requirements -->
              <div class="activity-wide-budget-wrapper mb-6">
                <div class="activity-wide-card p-6 rounded-2xl border border-purple-500/30 bg-slate-900/60 shadow-xl">
                  <div class="flex justify-between items-center mb-3 flex-wrap gap-3">
                    <div>
                      <label class="form-label !mb-0 flex items-center gap-2 text-xl font-extrabold text-white">
                        <span class="material-symbols-outlined text-[#c084fc] text-2xl">account_balance_wallet</span>
                        Activity-Wide / General Budget Requirements
                      </label>
                      <p class="text-sm text-slate-300 mt-1">Expenses generalized across the entire activity (speakers, tokens, supplies, main transportation, certificates).</p>
                    </div>
                    <div class="text-sm font-bold text-purple-200 bg-purple-500/20 border border-purple-500/40 px-3.5 py-1.5 rounded-xl font-mono">
                      General Expenses: ₱{{ formatNum(computeActivityBudgetTotal()) }}
                    </div>
                  </div>

                  <!-- Quick-Add Toolbar for Activity-Wide Items -->
                  <div class="quick-add-toolbar">
                    <button 
                      type="button" 
                      @click="toggleActivityBudgetItem('professional_fee')" 
                      class="quick-add-pill" 
                      :class="{ 'pill-active': activityBudget.active_items.professional_fee }"
                    >
                      <span class="pill-icon">{{ activityBudget.active_items.professional_fee ? '✓' : '+' }}</span> Professional Fee/Honoraria
                    </button>
                    <button 
                      type="button" 
                      @click="toggleActivityBudgetItem('tokens')" 
                      class="quick-add-pill" 
                      :class="{ 'pill-active': activityBudget.active_items.tokens }"
                    >
                      <span class="pill-icon">{{ activityBudget.active_items.tokens ? '✓' : '+' }}</span> Token/s
                    </button>
                    <button 
                      type="button" 
                      @click="toggleActivityBudgetItem('materials')" 
                      class="quick-add-pill" 
                      :class="{ 'pill-active': activityBudget.active_items.materials }"
                    >
                      <span class="pill-icon">{{ activityBudget.active_items.materials ? '✓' : '+' }}</span> Materials & Supplies
                    </button>
                    <button 
                      type="button" 
                      @click="toggleActivityBudgetItem('transportation')" 
                      class="quick-add-pill" 
                      :class="{ 'pill-active': activityBudget.active_items.transportation }"
                    >
                      <span class="pill-icon">{{ activityBudget.active_items.transportation ? '✓' : '+' }}</span> Transportation
                    </button>
                    <button 
                      type="button" 
                      @click="toggleActivityBudgetItem('function_room')" 
                      class="quick-add-pill" 
                      :class="{ 'pill-active': activityBudget.active_items.function_room }"
                    >
                      <span class="pill-icon">{{ activityBudget.active_items.function_room ? '✓' : '+' }}</span> Function Room / Hall
                    </button>
                    <button 
                      type="button" 
                      @click="toggleActivityBudgetItem('accommodation')" 
                      class="quick-add-pill" 
                      :class="{ 'pill-active': activityBudget.active_items.accommodation }"
                    >
                      <span class="pill-icon">{{ activityBudget.active_items.accommodation ? '✓' : '+' }}</span> Accommodation
                    </button>
                    <button 
                      type="button" 
                      @click="toggleActivityBudgetItem('equipment_rental')" 
                      class="quick-add-pill" 
                      :class="{ 'pill-active': activityBudget.active_items.equipment_rental }"
                    >
                      <span class="pill-icon">{{ activityBudget.active_items.equipment_rental ? '✓' : '+' }}</span> Equipment Rental
                    </button>
                    <button 
                      type="button" 
                      @click="toggleActivityBudgetItem('others')" 
                      class="quick-add-pill" 
                      :class="{ 'pill-active': activityBudget.active_items.others }"
                    >
                      <span class="pill-icon">{{ activityBudget.active_items.others ? '✓' : '+' }}</span> Other Expenses
                    </button>
                  </div>

                  <!-- Active Activity-Wide Items -->
                  <!-- Professional Fee / Honoraria -->
                  <div v-if="activityBudget.active_items.professional_fee" class="active-item-card mt-3">
                    <div class="flex justify-between items-center mb-2">
                      <div>
                        <span class="font-extrabold text-sm text-white">🎓 Professional Fee / Honoraria (Activity-Wide)</span>
                        <div class="text-xs text-emerald-400 font-semibold italic mt-0.5">(refer to BOR Resolution No. 2532, s. 2016, and DBM NBC 2007-1)</div>
                      </div>
                      <button type="button" @click="toggleActivityBudgetItem('professional_fee')" class="item-remove-badge" title="Remove">
                        <span>×</span> Remove
                      </button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-3">
                      <div>
                        <label class="section-field-label">Speakers</label>
                        <input type="number" v-model.number="activityBudget.professional_fee.speakers" min="1" placeholder="0" class="custom-input-field venue-input-large font-bold" />
                      </div>
                      <div>
                        <label class="section-field-label">Rate/Speaker (₱)</label>
                        <input type="number" v-model.number="activityBudget.professional_fee.rate" min="0" step="0.01" class="custom-input-field venue-input-large font-bold" />
                      </div>
                      <div>
                        <label class="section-field-label">Total (₱)</label>
                        <div class="custom-input-field venue-input-large flex items-center font-mono font-extrabold text-purple-200">
                          ₱{{ formatNum((Number(activityBudget.professional_fee.speakers) || 0) * (Number(activityBudget.professional_fee.rate) || 0)) }}
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Tokens -->
                  <div v-if="activityBudget.active_items.tokens" class="active-item-card mt-3">
                    <div class="flex justify-between items-center mb-3">
                      <div>
                        <span class="font-extrabold text-sm text-white">🎁 Token/s (Activity-Wide)</span>
                        <div class="text-xs text-emerald-400 font-semibold italic mt-0.5">(refer to Section 188 of NBC No. 594)</div>
                      </div>
                      <button type="button" @click="toggleActivityBudgetItem('tokens')" class="item-remove-badge" title="Remove">
                        <span>×</span> Remove
                      </button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                      <div>
                        <label class="section-field-label">Recipients</label>
                        <input type="number" v-model.number="activityBudget.tokens.recipients" min="1" placeholder="0" class="custom-input-field venue-input-large font-bold" />
                      </div>
                      <div>
                        <label class="section-field-label">Rate/Token (₱)</label>
                        <input type="number" v-model.number="activityBudget.tokens.rate" min="0" step="0.01" class="custom-input-field venue-input-large font-bold" />
                      </div>
                      <div>
                        <label class="section-field-label">Total (₱)</label>
                        <div class="custom-input-field venue-input-large flex items-center font-mono font-extrabold text-purple-200">
                          ₱{{ formatNum((Number(activityBudget.tokens.recipients) || 0) * (Number(activityBudget.tokens.rate) || 0)) }}
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Materials and Supplies -->
                  <div v-if="activityBudget.active_items.materials" class="active-item-card mt-3">
                    <div class="flex justify-between items-center mb-3">
                      <div>
                        <span class="font-extrabold text-sm text-white">📦 Materials and Supplies (Activity-Wide)</span>
                        <span class="text-xs text-slate-300 ml-2">(Auto-synced with total peak pax across all venues)</span>
                      </div>
                      <button type="button" @click="toggleActivityBudgetItem('materials')" class="item-remove-badge" title="Remove">
                        <span>×</span> Remove
                      </button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                      <div>
                        <label class="section-field-label">Participants</label>
                        <input type="number" v-model.number="activityBudget.materials.pax" :placeholder="String(totalActivityParticipants)" class="custom-input-field venue-input-large font-bold" />
                      </div>
                      <div>
                        <label class="section-field-label">Rate/Pax (₱)</label>
                        <input type="number" v-model.number="activityBudget.materials.rate" min="0" step="0.01" class="custom-input-field venue-input-large font-bold" />
                      </div>
                      <div>
                        <label class="section-field-label">Total (₱)</label>
                        <div class="custom-input-field venue-input-large flex items-center font-mono font-extrabold text-purple-200">
                          ₱{{ formatNum((Number(activityBudget.materials.pax || totalActivityParticipants) || 0) * (Number(activityBudget.materials.rate) || 0)) }}
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Transportation -->
                  <div v-if="activityBudget.active_items.transportation" class="active-item-card mt-3">
                    <div class="flex justify-between items-center mb-3">
                      <span class="font-extrabold text-sm text-white">🚐 Transportation (Activity-Wide)</span>
                      <button type="button" @click="toggleActivityBudgetItem('transportation')" class="item-remove-badge" title="Remove">
                        <span>×</span> Remove
                      </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <input type="text" v-model="activityBudget.transportation.description" placeholder="Route / Vehicle details (e.g. Van rental La Trinidad to Bokod and return)" class="custom-input-field venue-input-large" />
                      <div>
                        <div class="flex items-center gap-2">
                          <span class="text-slate-300 font-bold text-sm">₱</span>
                          <input type="number" v-model.number="activityBudget.transportation.total" min="0" step="0.01" placeholder="0.00" class="custom-input-field venue-input-large flex-1 font-bold" />
                        </div>
                        <div v-if="Number(activityBudget.transportation.total) > (baselineSettings?.transportation_limit || 20000)" class="text-xs text-red-300 font-bold mt-1.5 flex items-center gap-1">
                          ⚠️ Cannot exceed ₱{{ formatNum(baselineSettings?.transportation_limit || 20000) }} limit.
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Function Room / Hall (Activity-Wide) -->
                  <div v-if="activityBudget.active_items.function_room" class="active-item-card mt-3">
                    <div class="flex justify-between items-center mb-3">
                      <div>
                        <span class="font-extrabold text-sm text-white">🏨 Function Room / Hall Rental (Activity-Wide)</span>
                        <span class="text-xs text-slate-300 ml-2">(Leave 0 for free/attribution)</span>
                      </div>
                      <button type="button" @click="toggleActivityBudgetItem('function_room')" class="item-remove-badge" title="Remove">
                        <span>×</span> Remove
                      </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <input type="text" v-model="activityBudget.function_room.description" placeholder="Description / Hall name" class="custom-input-field venue-input-large" />
                      <div class="flex items-center gap-2">
                        <span class="text-slate-300 font-bold text-sm">₱</span>
                        <input type="number" v-model.number="activityBudget.function_room.cost" min="0" step="0.01" placeholder="0.00" class="custom-input-field venue-input-large flex-1 font-bold" />
                      </div>
                    </div>
                  </div>

                  <!-- Accommodation (Activity-Wide) -->
                  <div v-if="activityBudget.active_items.accommodation" class="active-item-card mt-3">
                    <div class="flex justify-between items-center mb-3">
                      <span class="font-extrabold text-sm text-white">🛏️ Accommodation (Activity-Wide)</span>
                      <button type="button" @click="toggleActivityBudgetItem('accommodation')" class="item-remove-badge" title="Remove">
                        <span>×</span> Remove
                      </button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                      <div>
                        <label class="section-field-label">Pax</label>
                        <input type="number" v-model.number="activityBudget.accommodation.pax" min="1" placeholder="Pax" class="custom-input-field venue-input-large" />
                      </div>
                      <div>
                        <label class="section-field-label">Nights</label>
                        <input type="number" v-model.number="activityBudget.accommodation.nights" min="1" placeholder="1" class="custom-input-field venue-input-large" />
                      </div>
                      <div>
                        <label class="section-field-label">Rate/Night (₱)</label>
                        <input type="number" v-model.number="activityBudget.accommodation.rate" min="0" step="0.01" placeholder="Rate" class="custom-input-field venue-input-large" />
                      </div>
                      <div>
                        <label class="section-field-label">Total (₱)</label>
                        <div class="custom-input-field venue-input-large flex items-center font-mono font-extrabold text-purple-200">
                          ₱{{ formatNum((Number(activityBudget.accommodation.pax) || 0) * (Number(activityBudget.accommodation.nights) || 0) * (Number(activityBudget.accommodation.rate) || 0)) }}
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Equipment Rental (Activity-Wide) -->
                  <div v-if="activityBudget.active_items.equipment_rental" class="active-item-card mt-3">
                    <div class="flex justify-between items-center mb-3">
                      <span class="font-extrabold text-sm text-white">🎤 Equipment Rental (Activity-Wide)</span>
                      <button type="button" @click="toggleActivityBudgetItem('equipment_rental')" class="item-remove-badge" title="Remove">
                        <span>×</span> Remove
                      </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <input type="text" v-model="activityBudget.equipment_rental.description" placeholder="Equipment details (e.g. Sounds & lights, Projector)" class="custom-input-field venue-input-large" />
                      <div class="flex items-center gap-2">
                        <span class="text-slate-300 font-bold text-sm">₱</span>
                        <input type="number" v-model.number="activityBudget.equipment_rental.cost" min="0" step="0.01" placeholder="0.00" class="custom-input-field venue-input-large flex-1 font-bold" />
                      </div>
                    </div>
                  </div>

                  <!-- Others (Activity-Wide) -->
                  <div v-if="activityBudget.active_items.others" class="active-item-card mt-3">
                    <div class="flex justify-between items-center mb-3">
                      <span class="font-extrabold text-sm text-white">📋 Other Expenses (Activity-Wide)</span>
                      <button type="button" @click="toggleActivityBudgetItem('others')" class="item-remove-badge" title="Remove">
                        <span>×</span> Remove
                      </button>
                    </div>
                    <div class="space-y-3">
                      <div v-for="(o, oIdx) in activityBudget.others" :key="oIdx" class="flex items-center gap-3">
                        <input type="text" v-model="o.name" placeholder="Item name (e.g. Certificate printing, Streamer)" class="custom-input-field venue-input-large flex-1" />
                        <div class="flex items-center gap-2 w-44">
                          <span class="text-slate-300 font-bold text-sm">₱</span>
                          <input type="number" v-model.number="o.amount" min="0" step="0.01" placeholder="0.00" class="custom-input-field venue-input-large flex-1 font-bold" />
                        </div>
                        <button type="button" @click="removeOtherItemFromActivity(oIdx)" class="item-line-remove-btn" title="Remove line item">
                          <span class="material-symbols-outlined text-base">close</span>
                        </button>
                      </div>
                      <button type="button" @click="addOtherItemToActivity" class="btn-add-line-item">
                        <span class="material-symbols-outlined text-base">add</span> Add Line Item
                      </button>
                    </div>
                  </div>

                </div>
              </div>

              <!-- Grand Total Banner Card -->
              <div class="grand-total-banner-card mt-6">
                <div class="flex justify-between items-center flex-wrap gap-4 w-full">
                  <div>
                    <div class="grand-total-label-banner">Grand Total Proposed Budget</div>
                    <div class="text-sm text-purple-200 mt-1 font-medium">
                      Across {{ venuesList.length }} {{ venuesList.length === 1 ? 'venue' : 'venues' }} • Total Target Participants: <b class="text-white font-extrabold">{{ totalActivityParticipants }} pax</b> <span class="text-xs text-purple-300/80">(sum of peak pax per venue)</span>
                    </div>
                  </div>
                  <div class="grand-total-value-banner">
                    ₱{{ formatNum(grandTotalProposedBudget) }}
                  </div>
                </div>
              </div>

              <!-- Official GAD Budget Table Preview (IX. BUDGETARY REQUIREMENTS) -->
              <div class="official-preview-card mt-6">
                <div class="flex justify-between items-center mb-4">
                  <div class="flex items-center gap-2">
                    <span class="text-base font-extrabold text-amber-300 uppercase tracking-wider">IX. BUDGETARY REQUIREMENTS (Official Format Preview)</span>
                  </div>
                  <span class="text-xs text-amber-200/80 font-medium">Live preview of official GAD report</span>
                </div>

                <div class="overflow-x-auto rounded-xl border border-amber-500/40">
                  <table class="w-full text-left text-sm border-collapse">
                    <thead>
                      <tr class="bg-amber-500/25 text-amber-200 font-extrabold border-b border-amber-500/40 uppercase tracking-wider text-xs">
                        <th class="p-3.5 border-r border-amber-500/30 w-1/4">Item / Description</th>
                        <th class="p-3.5 border-r border-amber-500/30 w-1/2">Breakdown / Formula</th>
                        <th class="p-3.5 text-right w-1/4">Proposed Budget</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10 text-slate-100 bg-slate-900/60">
                      <!-- Meals and Snacks across all venues -->
                      <tr class="hover:bg-white/5 transition-colors">
                        <td class="p-3.5 align-top font-bold text-white border-r border-white/10">
                          Meals and Snacks (AM/PM)
                          <div class="text-xs text-emerald-400 font-normal italic mt-1">
                            (refer to E.O No. 77 and BSU Office Memorandum 117, s. 2024)
                          </div>
                        </td>
                        <td class="p-3.5 align-top border-r border-white/10">
                          <div v-for="(vCard, vIdx) in venuesList" :key="'preview-v-'+vIdx" class="mb-3.5 last:mb-0">
                            <div class="font-bold text-purple-300 mb-1 text-sm">{{ getVenueDisplayName(vCard) }}:</div>
                            <div class="space-y-1 text-slate-200 pl-3 border-l-2 border-purple-500/40">
                              <template v-for="mKey in ['breakfast', 'am_snack', 'lunch', 'pm_snack', 'dinner']" :key="mKey">
                                <div v-if="getVenueMealDays(vCard, mKey) > 0" class="text-xs sm:text-sm">
                                  <span class="capitalize font-semibold text-white">{{ mKey.replace('_', ' ') }}</span>: {{ getVenueMealPax(vCard, mKey) }} pax × ₱{{ formatNum(getVenueMealRate(vCard, mKey)) }} × {{ getVenueMealDays(vCard, mKey) }} {{ getVenueMealDays(vCard, mKey) === 1 ? 'day' : 'days' }} = <span class="font-mono font-bold text-purple-200">₱{{ formatNum(computeVenueMealSubtotal(vCard, mKey)) }}</span>
                                </div>
                              </template>
                              <div v-if="!hasAnyMealsScheduledInVenue(vCard)" class="text-slate-400 italic text-xs">No meals scheduled</div>
                            </div>
                          </div>
                        </td>
                        <td class="p-3.5 align-top text-right font-mono font-bold text-purple-200">
                          <div v-for="(vCard, vIdx) in venuesList" :key="'preview-vt-'+vIdx" class="mb-3.5 last:mb-0">
                            <div class="text-xs text-slate-300 font-normal">{{ getVenueDisplayName(vCard) }}:</div>
                            <span class="text-sm font-bold text-white">₱{{ formatNum(computeVenueMealsTotal(vCard)) }}</span>
                          </div>
                        </td>
                      </tr>

                      <!-- Function Room / Venue -->
                      <tr v-if="hasAnyItemForCategory('function_room')" class="hover:bg-white/5 transition-colors">
                        <td class="p-3.5 font-bold text-white border-r border-white/10">Function Room/Venue</td>
                        <td class="p-3.5 border-r border-white/10 text-slate-200 text-sm">
                          <div v-for="v in getActiveVenuesForItem('function_room')" :key="v.id">
                            <span class="font-semibold text-purple-300">[{{ getVenueDisplayName(v) }}]:</span> {{ v.function_room.description || 'Function Room' }}
                          </div>
                          <div v-if="activityBudget.active_items?.function_room">
                            <span class="font-semibold text-amber-300">[Activity-Wide]:</span> {{ activityBudget.function_room.description || 'Function Room' }}
                          </div>
                        </td>
                        <td class="p-3.5 text-right font-mono font-bold text-white text-sm">
                          ₱{{ formatNum(sumCategoryTotal('function_room')) }}
                        </td>
                      </tr>

                      <!-- Accommodation -->
                      <tr v-if="hasAnyItemForCategory('accommodation')" class="hover:bg-white/5 transition-colors">
                        <td class="p-3.5 font-bold text-white border-r border-white/10">Accommodation</td>
                        <td class="p-3.5 border-r border-white/10 text-slate-200 text-sm">
                          <div v-for="v in getActiveVenuesForItem('accommodation')" :key="v.id">
                            <span class="font-semibold text-purple-300">[{{ getVenueDisplayName(v) }}]:</span> {{ v.accommodation.pax }} pax × {{ v.accommodation.nights }} nights @ ₱{{ formatNum(v.accommodation.rate) }}
                          </div>
                          <div v-if="activityBudget.active_items?.accommodation">
                            <span class="font-semibold text-amber-300">[Activity-Wide]:</span> {{ activityBudget.accommodation.pax }} pax × {{ activityBudget.accommodation.nights }} nights @ ₱{{ formatNum(activityBudget.accommodation.rate) }}
                          </div>
                        </td>
                        <td class="p-3.5 text-right font-mono font-bold text-white text-sm">
                          ₱{{ formatNum(sumCategoryTotal('accommodation')) }}
                        </td>
                      </tr>

                      <!-- Equipment Rental -->
                      <tr v-if="hasAnyItemForCategory('equipment_rental')" class="hover:bg-white/5 transition-colors">
                        <td class="p-3.5 font-bold text-white border-r border-white/10">Equipment Rental</td>
                        <td class="p-3.5 border-r border-white/10 text-slate-200 text-sm">
                          <div v-for="v in getActiveVenuesForItem('equipment_rental')" :key="v.id">
                            <span class="font-semibold text-purple-300">[{{ getVenueDisplayName(v) }}]:</span> {{ v.equipment_rental.description || 'Equipment rental' }}
                          </div>
                          <div v-if="activityBudget.active_items?.equipment_rental">
                            <span class="font-semibold text-amber-300">[Activity-Wide]:</span> {{ activityBudget.equipment_rental.description || 'Equipment rental' }}
                          </div>
                        </td>
                        <td class="p-3.5 text-right font-mono font-bold text-white text-sm">
                          ₱{{ formatNum(sumCategoryTotal('equipment_rental')) }}
                        </td>
                      </tr>

                      <!-- Professional Fee / Honoraria -->
                      <tr v-if="hasAnyItemForCategory('professional_fee')" class="hover:bg-white/5 transition-colors">
                        <td class="p-3.5 font-bold text-white border-r border-white/10">
                          Professional Fee/Honoraria
                          <div class="text-xs text-emerald-400 font-normal italic mt-1">
                            (refer to BOR Resolution No. 2532, s. 2016, and DBM NBC 2007-1)
                          </div>
                        </td>
                        <td class="p-3.5 border-r border-white/10 text-slate-200 text-sm">
                          <div v-for="v in getActiveVenuesForItem('professional_fee')" :key="v.id">
                            <span class="font-semibold text-purple-300">[{{ getVenueDisplayName(v) }}]:</span> {{ v.professional_fee.speakers }} speaker(s) @ ₱{{ formatNum(v.professional_fee.rate) }}
                          </div>
                          <div v-if="activityBudget.active_items?.professional_fee">
                            <span class="font-semibold text-amber-300">[Activity-Wide]:</span> {{ activityBudget.professional_fee.speakers }} speaker(s) @ ₱{{ formatNum(activityBudget.professional_fee.rate) }}
                          </div>
                        </td>
                        <td class="p-3.5 text-right font-mono font-bold text-white text-sm">
                          ₱{{ formatNum(sumCategoryTotal('professional_fee')) }}
                        </td>
                      </tr>

                      <!-- Tokens -->
                      <tr v-if="hasAnyItemForCategory('tokens')" class="hover:bg-white/5 transition-colors">
                        <td class="p-3.5 font-bold text-white border-r border-white/10">
                          Token/s
                          <div class="text-xs text-emerald-400 font-normal italic mt-1">
                            (refer to Section 188 of NBC No. 594)
                          </div>
                        </td>
                        <td class="p-3.5 border-r border-white/10 text-slate-200 text-sm">
                          <div v-for="v in getActiveVenuesForItem('tokens')" :key="v.id">
                            <span class="font-semibold text-purple-300">[{{ getVenueDisplayName(v) }}]:</span> {{ v.tokens.recipients }} recipient(s) @ ₱{{ formatNum(v.tokens.rate) }}
                          </div>
                          <div v-if="activityBudget.active_items?.tokens">
                            <span class="font-semibold text-amber-300">[Activity-Wide]:</span> {{ activityBudget.tokens.recipients }} recipient(s) @ ₱{{ formatNum(activityBudget.tokens.rate) }}
                          </div>
                        </td>
                        <td class="p-3.5 text-right font-mono font-bold text-white text-sm">
                          ₱{{ formatNum(sumCategoryTotal('tokens')) }}
                        </td>
                      </tr>

                      <!-- Materials & Supplies -->
                      <tr v-if="hasAnyItemForCategory('materials')" class="hover:bg-white/5 transition-colors">
                        <td class="p-3.5 font-bold text-white border-r border-white/10">Materials and Supplies</td>
                        <td class="p-3.5 border-r border-white/10 text-slate-200 text-sm">
                          <div v-for="v in getActiveVenuesForItem('materials')" :key="v.id">
                            <span class="font-semibold text-purple-300">[{{ getVenueDisplayName(v) }}]:</span> {{ v.materials.pax || getVenueMaxPax(v) }} pax @ ₱{{ formatNum(v.materials.rate) }}
                          </div>
                          <div v-if="activityBudget.active_items?.materials">
                            <span class="font-semibold text-amber-300">[Activity-Wide]:</span> {{ activityBudget.materials.pax || totalActivityParticipants }} pax @ ₱{{ formatNum(activityBudget.materials.rate) }}
                          </div>
                        </td>
                        <td class="p-3.5 text-right font-mono font-bold text-white text-sm">
                          ₱{{ formatNum(sumCategoryTotal('materials')) }}
                        </td>
                      </tr>

                      <!-- Transportation -->
                      <tr v-if="hasAnyItemForCategory('transportation')" class="hover:bg-white/5 transition-colors">
                        <td class="p-3.5 font-bold text-white border-r border-white/10">Transportation</td>
                        <td class="p-3.5 border-r border-white/10 text-slate-200 text-sm">
                          <div v-for="v in getActiveVenuesForItem('transportation')" :key="v.id">
                            <span class="font-semibold text-purple-300">[{{ getVenueDisplayName(v) }}]:</span> {{ v.transportation.description || 'Transportation' }}
                          </div>
                          <div v-if="activityBudget.active_items?.transportation">
                            <span class="font-semibold text-amber-300">[Activity-Wide]:</span> {{ activityBudget.transportation.description || 'Transportation' }}
                          </div>
                        </td>
                        <td class="p-3.5 text-right font-mono font-bold text-white text-sm">
                          ₱{{ formatNum(sumCategoryTotal('transportation')) }}
                        </td>
                      </tr>

                      <!-- Others -->
                      <tr v-if="hasAnyItemForCategory('others')" class="hover:bg-white/5 transition-colors">
                        <td class="p-3.5 font-bold text-white border-r border-white/10">Others</td>
                        <td class="p-3.5 border-r border-white/10 text-slate-200 text-sm">
                          <div v-for="v in getActiveVenuesForItem('others')" :key="v.id">
                            <span v-for="(o, oIdx) in (v.others || []).filter(x => x.name && Number(x.amount) > 0)" :key="oIdx" class="mr-4 inline-block">
                              <span class="font-semibold text-purple-300">[{{ getVenueDisplayName(v) }} - {{ o.name }}]:</span> ₱{{ formatNum(o.amount) }}
                            </span>
                          </div>
                          <div v-if="activityBudget.active_items?.others">
                            <span v-for="(o, oIdx) in (activityBudget.others || []).filter(x => x.name && Number(x.amount) > 0)" :key="'ab-o-'+oIdx" class="mr-4 inline-block">
                              <span class="font-semibold text-amber-300">[Activity-Wide - {{ o.name }}]:</span> ₱{{ formatNum(o.amount) }}
                            </span>
                          </div>
                        </td>
                        <td class="p-3.5 text-right font-mono font-bold text-white text-sm">
                          ₱{{ formatNum(sumCategoryTotal('others')) }}
                        </td>
                      </tr>

                      <!-- Grand Total Row -->
                      <tr class="bg-amber-500/25 font-extrabold text-amber-200 text-base">
                        <td colspan="2" class="p-4 uppercase tracking-wider border-r border-amber-500/30">TOTAL PROPOSED BUDGET</td>
                        <td class="p-4 text-right font-mono text-lg font-black text-amber-100">
                          ₱{{ formatNum(grandTotalProposedBudget) }}
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Visual Section Divider -->
              <div class="form-section-divider">
                <div class="form-section-divider-line"></div>
              </div>
<!-- SECTION 4: Upload Activity Design (PDF) (Full Width) -->
              <div class="attachment-full-wrapper mb-6">
                <label class="form-label !mb-2">Upload Activity Design (PDF) *</label>
                <div class="attachment-display-grid">
                  <div class="attachment-upload-column">
                    <div class="upload-dropzone" @click="$refs.fileInput.click()">
                      <input ref="fileInput" type="file" @change="handleFileUpload" accept=".pdf" style="display: none;" />
                      <span class="upload-icon">📤</span>
                      <p class="upload-text">Upload Activity Design Document</p>
                      <p class="upload-hint">PDF format (Max 10MB)</p>
                    </div>
                  </div>
                  <div class="attachment-preview-column">
                    <div v-if="designFile" class="uploaded-file-display" style="flex-direction: column; align-items: flex-start;">
                      <div class="uploaded-file-tag" style="width: 100%;">
                        <span class="uploaded-file-name">📄 {{ designFile.name }}</span>
                        <button type="button" @click="removeFile" class="remove-file-btn">Remove</button>
                      </div>
                      <!-- Document Previews -->
                      <div class="document-previews" style="margin-top: 15px; width: 100%;" v-if="designFile.previewUrl">
                        <div class="flex justify-between items-center mb-2">
                          <p style="color: #b979cc; font-size: 13px; font-weight: bold; margin-bottom: 0;">Document Preview:</p>
                          <button @click.prevent="expandToNewTab(designFile.previewUrl)" style="background: rgba(185, 121, 204, 0.1); border: 1px solid rgba(185, 121, 204, 0.3); color: #e9d5ff; padding: 4px 12px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; font-size: 13px;">
                            <span class="material-symbols-outlined" style="font-size: 14px; margin-right: 4px;">open_in_new</span> Expand
                          </button>
                        </div>
                        <iframe :src="getPdfViewerUrl(designFile.previewUrl)" width="100%" height="400px" style="border: 1px solid #b979cc; border-radius: 8px;"></iframe>
                      </div>
                    </div>
                    <p v-else class="no-file-uploaded-text">No file uploaded yet.</p>
                  </div>
                </div>
              </div>



              <div class="form-actions">
                <button 
                  type="button"
                  @click="goBack" 
                  class="back-button"
                >
                  &#8592; Back
                </button>
                <button 
                  type="submit" 
                  class="submit-action-btn"
                >
                  Submit Design &#8594;
                </button>
              </div>
            </form>
          </div>
        </div>
      </main>
</template>

<script setup>
import { useHolidays } from '../../utils/useHolidays';
const { isDisabledDate, fetchHolidays, getWorkingDaysDiff, addWorkingDays } = useHolidays();
import { ref, onMounted, onUnmounted, computed, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import Swal from 'sweetalert2';
import api from '../../api';
import CustomSelect from '../../components/CustomSelect.vue';

const router = useRouter();
const route = useRoute();
const user = ref(JSON.parse(localStorage.getItem('user') || '{}'));

const getTodayDate = () => {
  const d = new Date();
  const utc = d.getTime() + (d.getTimezoneOffset() * 60000);
  const phDate = new Date(utc + (3600000 * 8));
  const year = phDate.getUTCFullYear();
  const month = String(phDate.getUTCMonth() + 1).padStart(2, '0');
  const day = String(phDate.getUTCDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
};
const todayDate = ref(getTodayDate());

const maxDateLimit = computed(() => {
  const currentYear = new Date().getFullYear();
  return `${currentYear}-12-31`;
});
const minStartDate = computed(() => {
  const today = new Date(new Date().toLocaleString("en-US", { timeZone: "Asia/Manila" }));
  today.setHours(0, 0, 0, 0);
  const targetDate = addWorkingDays(today, 3);
  const yearStr = targetDate.getFullYear();
  const monthStr = String(targetDate.getMonth() + 1).padStart(2, '0');
  const dayStr = String(targetDate.getDate()).padStart(2, '0');
  return `${yearStr}-${monthStr}-${dayStr}`;
});

const helpState = ref({
  startDate: false,
  endDate: false,
  startTime: false,
  endTime: false,
  targetParticipants: false
});

const toggleHelp = (field) => {
  const currentVal = helpState.value[field];
  Object.keys(helpState.value).forEach(key => {
    helpState.value[key] = false;
  });
  helpState.value[field] = !currentVal;
};

const closeAllHelp = () => {
  Object.keys(helpState.value).forEach(key => {
    helpState.value[key] = false;
  });
};



const isCurrentYear = (dateString) => {
  const date = new Date(dateString + 'T00:00:00');
  const manilaTime = new Date().toLocaleString("en-US", { timeZone: "Asia/Manila" });
  const currentYear = new Date(manilaTime).getFullYear();
  return date.getFullYear() === currentYear;
};

const isValidActivityDate = (dateString, checkLeadTime = false) => {
  if (!isCurrentYear(dateString)) {
    const currentYear = new Date().getFullYear();
    return { valid: false, reason: `Activities can only be scheduled in ${currentYear}. Please select a date within the current year.` };
  }
  if (checkLeadTime) {
    const targetDate = new Date(dateString + 'T00:00:00');
    const today = new Date(new Date().toLocaleString("en-US", { timeZone: "Asia/Manila" }));
    today.setHours(0, 0, 0, 0);
    const diffDays = getWorkingDaysDiff(today, targetDate);
    
    if (diffDays < 3) {
       return { valid: false, reason: `Activities must be scheduled at least 3 working days in advance.` };
    } else if (diffDays < 15) {
       return { valid: true, reason: `Activities should ideally be scheduled at least 15 working days in advance.`, isWarning: true };
    }
  }
  return { valid: true, reason: '' };
};

const isValidActivityDuration = (startDateString, endDateString) => {
  if (!startDateString || !endDateString) {
    return { valid: true, reason: '', isWarning: false };
  }
  const startDate = new Date(startDateString + 'T00:00:00');
  const endDate = new Date(endDateString + 'T00:00:00');
  
  if (endDate < startDate) {
    return { valid: false, reason: 'End date cannot be before start date.', isWarning: false };
  }
  
  const diffTime = endDate - startDate;
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  
  if (diffDays > 31) {
    return { valid: true, reason: 'Are you sure if the activity is more than 1 month?', isWarning: true };
  }
  
  return { valid: true, reason: '', isWarning: false };
};

const venues = ref([]);
const customVenue = ref('');
const formTypes = ref([]);
const GADMandates = ref([]);
const genderIssues = ref([]);
const genderIssueOptions = computed(() => {
  const opts = genderIssues.value.map(issue => ({
    value: issue.id,
    label: issue.title
  }));
  opts.push({ value: 'Other', label: 'Other' });
  return opts;
});
const ActClassification = ref([]); 
const customMandate = ref('');
const customGenderIssue = ref('');

const scheduleType = ref('staggered');
const continuousConfig = ref({
  start_date: '',
  end_date: '',
  start_time: '',
  end_time: '',
  meals_and_snacks: { breakfast: false, am_snack: false, lunch: false, pm_snack: false, dinner: false }
});

const schedules = ref([{ date: '', start_time: '', end_time: '', meals_and_snacks: { breakfast: false, am_snack: false, lunch: false, pm_snack: false, dinner: false } }]);

const validateTimeBounds = (timeStr, fieldName, schObj, fieldKey) => {
  if (!timeStr) return true;
  const [h, m] = timeStr.split(':').map(Number);
  if (h < 4 || h > 20 || (h === 20 && m > 0)) {
    document.activeElement?.blur();
    Swal.fire({
      icon: 'warning',
      title: 'Invalid Time',
      text: `${fieldName} must be between 04:00 AM and 08:00 PM.`,
      confirmButtonColor: '#b979cc'
    });
    schObj[fieldKey] = '';
    return false;
  }
  return true;
};

const handleTimeChange = (sch) => {
  if (sch.start_time && !validateTimeBounds(sch.start_time, 'Start Time', sch, 'start_time')) return;
  if (sch.end_time && !validateTimeBounds(sch.end_time, 'End Time', sch, 'end_time')) return;
  
  if (sch.start_time && sch.end_time) {
    const startTimeParts = sch.start_time.split(':');
    const endTimeParts = sch.end_time.split(':');
    const startMinutes = parseInt(startTimeParts[0]) * 60 + parseInt(startTimeParts[1]);
    const endMinutes = parseInt(endTimeParts[0]) * 60 + parseInt(endTimeParts[1]);
    
    if (endMinutes <= startMinutes) {
      document.activeElement?.blur();
      Swal.fire({ icon: 'warning', title: 'Invalid Time Range', text: 'End time must be after start time.', confirmButtonColor: '#b979cc' });
      sch.end_time = '';
      return;
    } else if ((endMinutes - startMinutes) < 60) {
      document.activeElement?.blur();
      Swal.fire({ icon: 'warning', title: 'Invalid Time Range', text: 'The activity duration must be at least 1 hour.', confirmButtonColor: '#b979cc' });
      sch.end_time = '';
      return;
    }
  }

  if (!sch.start_time || !sch.end_time) return;
  const [startH] = sch.start_time.split(':').map(Number);
  const [endH, endM] = sch.end_time.split(':').map(Number);
  if (startH >= 13) {
    sch.meals_and_snacks.breakfast = false;
    sch.meals_and_snacks.am_snack = false;
  }
  if (endH < 12 || (endH === 12 && endM === 0)) {
    sch.meals_and_snacks.pm_snack = false;
    sch.meals_and_snacks.dinner = false;
  }
};

const form = ref({
  form_type: '',
  nature: '',
  activity_classification_id: '',
  gad_mandate_id: '',
  gender_issue_id: '',
  activity_title: '',
  start_date: '',
  end_date: '',
  start_time: '',
  end_time: '',
  venue: '',
  is_inside_bsu: true,
  target_participants: '',
  proposed_budget: 0,
  budget_items: [
    { name: 'Meals', total: '' },
    { name: 'Snacks', total: '' },
    { name: 'Function Room/Venue', total: '' },
    { name: 'Accommodation', total: '' },
    { name: 'Equipment Rental', total: '' },
    { name: 'Professional Fee/Honoraria', total: '' },
    { name: 'Token/s', total: '' },
    { name: 'Materials and Supplies', total: '' },
    { name: 'Transportation', total: '' },
    { name: 'Others', total: '' }
  ]
});

const designFile = ref(null);
const fileInput = ref(null);

const userRole = user.value?.role || user.value?.user_role || '';
const getPdfViewerUrl = (url) => {
  if (!url) return '';
  return `/pdfjs/web/viewer.html?file=${encodeURIComponent(url)}&role=${encodeURIComponent(userRole)}`;
};

const expandToNewTab = (url) => {
  if (url) {
    window.open(getPdfViewerUrl(url), '_blank');
  }
};

const handleFileUpload = (event) => {
  if (event.target.files.length > 0) {
    const file = event.target.files[0];
    
    if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
      Swal.fire({
        icon: 'error',
        title: 'Invalid File Type',
        text: 'Only PDF files are allowed.',
        confirmButtonColor: '#b979cc'
      });
      removeFile();
      return;
    }

    if (file.size > 10 * 1024 * 1024) {
      Swal.fire({
        icon: 'error',
        title: 'File Too Large',
        text: 'The file size must not exceed 10 MB.',
        confirmButtonColor: '#b979cc'
      });
      removeFile();
      return;
    }

    designFile.value = file;
    file.previewUrl = URL.createObjectURL(file);
  }
};

const removeFile = () => {
  designFile.value = null;
  if (fileInput.value) fileInput.value.value = '';
};

const formatBudgetName = (name) => {
  if (!name) return '';
  return name.replace(/(\(.*\))/g, '<span class="budget-item-subtext">$1</span>');
};

const fetchVenues = async () => {
  try {
    const response = await api.get('venues');
    if (Array.isArray(response.data)) {
      venues.value = response.data;
    } else if (response.data && response.data.success) {
      venues.value = response.data.data || [];
    }
  } catch (error) {
    console.error('Error fetching venues:', error);
  }
};

const filteredVenues = computed(() => {
  return venues.value.filter(v => (v.is_inside_bsu == 1 || v.is_inside_bsu === true) === form.value.is_inside_bsu);
});

watch(() => form.value.is_inside_bsu, () => {
  if (form.value.venue && form.value.venue !== 'Other') {
    const isValid = filteredVenues.value.some(v => v.venue_id == form.value.venue);
    if (!isValid) {
      form.value.venue = '';
    }
  }
});

const fetchFormTypes = async () => {
  try {
    const res = await api.get('get-form-types');
    formTypes.value = res.data;
  } catch (error) {
    console.error('Error fetching form types:', error);
  }
};

const fetchActivityClassifications = async () => {
  try {
    const res = await api.get('get-activity-classifications');
    ActClassification.value = res.data;
  } catch (error) {
    console.error('Error fetching activity classifications:', error);
  }
};

const fetchGADMandates = async () => {
    try {
      let url = 'get-gad-mandates';
        if (form.value && form.value.activity_classification_id) {
            url += '?classification=' + form.value.activity_classification_id;
        }
      const res = await api.get(url);
    GADMandates.value = res.data;
  } catch (error) {
    console.error('Error fetching GAD mandates:', error);
  }
};

const fetchGenderIssues = async (mandateIds) => {
  let ids = mandateIds || form.value?.gad_mandate_id || gad_mandate_id?.value;
  if (typeof ids === 'string' || typeof ids === 'number') ids = [ids];
  if (!ids || !Array.isArray(ids) || ids.length === 0 ) {
    genderIssues.value = [];
    return;
  }
  try {
    const allIssues = [];
    for (const mandateId of ids) {
       if (mandateId !== 'Other') {
           let url = `get-gender-issues?mandates=${mandateId}`;
             if (form.value && form.value.activity_classification_id) {
                 url += '&classification=' + form.value.activity_classification_id;
             }
             const res = await api.get(url);
           allIssues.push(...res.data);
       }
    }
    genderIssues.value = allIssues;
  } catch (error) {
    console.error('Error fetching gender issues:', error);
  }
};

watch(() => form.value.activity_classification_id, (newVal) => {
    form.value.gad_mandate_id = '';
    form.value.gender_issue_id = '';
    fetchGADMandates();
  });

  watch(() => form.value.gad_mandate_id, (newVal) => {
  form.value.gender_issue_id = '';
  fetchGenderIssues(newVal);
});

watch(() => form.value.budget_items, (newItems) => {
  const total = newItems.reduce((sum, item) => sum + (Number(item.total) || 0), 0);
  form.value.proposed_budget = total;
}, { deep: true });

// Baseline Settings
const baselineSettings = ref({
  meals_inside: 220,
  meals_outside: 350,
  snacks_inside: 80,
  snacks_outside: 150,
  pf_honoraria: 2258.25,
  tokens: 1000,
  materials: 1000,
  transportation_limit: 20000
});

const fetchBaselineSettings = async () => {
  try {
    const res = await api.get('/settings/baseline');
    if (res.data) {
      baselineSettings.value = { ...baselineSettings.value, ...res.data };
      if (venuesList.value && venuesList.value[0]) {
        handleVenueLocationToggle(venuesList.value[0], venuesList.value[0].is_inside_bsu);
      }
    }
  } catch (error) {
    console.error('Failed to fetch baseline settings:', error);
  }
};

const adSubmissionLimitEnabled = ref(true);

const fetchSystemSettings = async () => {
  try {
    const res = await api.get('settings/system');
    adSubmissionLimitEnabled.value = res.data?.ad_submission_limit_enabled ?? true;
  } catch (err) {
    console.error('Failed to fetch system settings:', err);
  }
};

// Schedule Row & Multi-Venue State Factory
const createNewScheduleRow = () => ({
  date: '',
  start_time: '08:00',
  end_time: '17:00',
  meals_and_snacks: {
    breakfast: false,
    am_snack: true,
    lunch: true,
    pm_snack: false,
    dinner: false
  }
});

const createNewVenue = (id = 1) => ({
  id,
  is_inside_bsu: true,
  venue_id: '',
  custom_venue: '',
  constant_pax: '',
  schedule_type: 'staggered',
  continuous_config: {
    start_date: '',
    end_date: '',
    start_time: '08:00',
    end_time: '17:00',
    meals_and_snacks: { breakfast: false, am_snack: true, lunch: true, pm_snack: false, dinner: false }
  },
  schedules: [createNewScheduleRow()],
  meals_custom: {
    breakfast: { pax: '', cost: baselineSettings.value?.meals_inside || 220 },
    am_snack: { pax: '', cost: baselineSettings.value?.snacks_inside || 80 },
    lunch: { pax: '', cost: baselineSettings.value?.meals_inside || 220 },
    pm_snack: { pax: '', cost: baselineSettings.value?.snacks_inside || 80 },
    dinner: { pax: '', cost: baselineSettings.value?.meals_inside || 220 }
  },
  active_items: {
    function_room: false,
    accommodation: false,
    equipment_rental: false,
    transportation: false,
    others: false
  },
  function_room: { description: '', cost: '' },
  accommodation: { description: '', pax: '', nights: 1, rate: 1500 },
  equipment_rental: { description: '', cost: '' },
  transportation: { description: '', total: '' },
  others: [{ name: '', amount: '' }]
});

const venuesList = ref([createNewVenue(1)]);
let nextVenueId = 2;

const addVenue = () => {
  const v = createNewVenue(nextVenueId++);
  venuesList.value.push(v);
};

const removeVenue = (index) => {
  if (venuesList.value.length > 1) {
    venuesList.value.splice(index, 1);
  }
};

const getFilteredVenuesForCard = (vCard) => {
  return venues.value.filter(v => (v.is_inside_bsu == 1 || v.is_inside_bsu === true) === vCard.is_inside_bsu);
};

const handleVenueLocationToggle = (vCard, isInside) => {
  vCard.is_inside_bsu = isInside;
  vCard.venue_id = '';
  vCard.custom_venue = '';
  const mealRate = isInside ? (baselineSettings.value?.meals_inside || 220) : (baselineSettings.value?.meals_outside || 350);
  const snackRate = isInside ? (baselineSettings.value?.snacks_inside || 80) : (baselineSettings.value?.snacks_outside || 150);
  
  if (vCard.meals_custom.breakfast) vCard.meals_custom.breakfast.cost = mealRate;
  if (vCard.meals_custom.lunch) vCard.meals_custom.lunch.cost = mealRate;
  if (vCard.meals_custom.dinner) vCard.meals_custom.dinner.cost = mealRate;
  if (vCard.meals_custom.am_snack) vCard.meals_custom.am_snack.cost = snackRate;
  if (vCard.meals_custom.pm_snack) vCard.meals_custom.pm_snack.cost = snackRate;
};

// Schedules per venue helpers
const addScheduleToVenue = (vCard) => {
  vCard.schedules.push(createNewScheduleRow());
};

const removeScheduleFromVenue = (vCard, sIdx) => {
  if (vCard.schedules.length > 1) {
    vCard.schedules.splice(sIdx, 1);
  }
};

const handleVenueScheduleTypeChange = (vCard, newType) => {
  vCard.schedule_type = newType;
  if (newType === 'continuous') {
    generateConsecutiveSchedulesForVenue(vCard);
  }
};

const generateConsecutiveSchedulesForVenue = (vCard) => {
  const config = vCard.continuous_config;
  if (!config.start_date || !config.end_date) return;
  const start = new Date(config.start_date);
  const end = new Date(config.end_date);
  if (end < start) return;

  const generated = [];
  let current = new Date(start);
  while (current <= end) {
    if (!isDisabledDate(current)) {
      const dateStr = current.toISOString().split('T')[0];
      generated.push({
        date: dateStr,
        start_time: config.start_time || '08:00',
        end_time: config.end_time || '17:00',
        meals_and_snacks: { ...config.meals_and_snacks }
      });
    }
    current.setDate(current.getDate() + 1);
  }
  if (generated.length > 0) {
    vCard.schedules = generated;
  }
};

const handleVenueScheduleTimeChange = (vCard, sIdx) => {
  const sch = vCard.schedules[sIdx];
  if (!sch) return;
  handleTimeChange(sch);
};

// Auto-meal computation per venue
const getVenueMealDays = (vCard, mealKey) => {
  if (!vCard?.schedules) return 0;
  return vCard.schedules.filter(s => s.date && s.meals_and_snacks && s.meals_and_snacks[mealKey]).length;
};

const hasAnyMealsScheduledInVenue = (vCard) => {
  return ['breakfast', 'am_snack', 'lunch', 'pm_snack', 'dinner'].some(m => getVenueMealDays(vCard, m) > 0);
};

// Dynamic Venue Max Pax & Constant Pax Helper
const getVenueMaxPax = (vCard) => {
  if (!vCard) return 0;
  const activeMeals = ['breakfast', 'am_snack', 'lunch', 'pm_snack', 'dinner']
    .filter(m => getVenueMealDays(vCard, m) > 0);

  if (activeMeals.length === 0) {
    return Number(vCard.constant_pax) || 0;
  }

  const paxList = activeMeals.map(m => {
    const custom = vCard.meals_custom?.[m]?.pax;
    if (custom !== undefined && custom !== '' && custom !== null) {
      return Number(custom) || 0;
    }
    return Number(vCard.constant_pax) || 0;
  });

  return Math.max(...paxList, Number(vCard.constant_pax) || 0);
};

const applyConstantPaxToVenue = (vCard) => {
  const cp = Number(vCard.constant_pax) || 0;
  if (cp <= 0) return;
  ['breakfast', 'am_snack', 'lunch', 'pm_snack', 'dinner'].forEach(m => {
    if (vCard.meals_custom[m]) {
      vCard.meals_custom[m].pax = cp;
    }
  });
};

const getVenueMealPax = (vCard, mealKey) => {
  const custom = vCard.meals_custom?.[mealKey]?.pax;
  if (custom !== undefined && custom !== '' && custom !== null) {
    return Number(custom);
  }
  return Number(vCard.constant_pax) || 0;
};

const getVenueMealRate = (vCard, mealKey) => {
  const custom = vCard.meals_custom?.[mealKey]?.cost;
  if (custom !== undefined && custom !== '' && custom !== null) {
    return Number(custom);
  }
  const isInside = vCard.is_inside_bsu;
  const isSnack = mealKey === 'am_snack' || mealKey === 'pm_snack';
  if (isSnack) {
    return isInside ? (baselineSettings.value?.snacks_inside || 80) : (baselineSettings.value?.snacks_outside || 150);
  }
  return isInside ? (baselineSettings.value?.meals_inside || 220) : (baselineSettings.value?.meals_outside || 350);
};

const computeVenueMealSubtotal = (vCard, mealKey) => {
  const days = getVenueMealDays(vCard, mealKey);
  if (days <= 0) return 0;
  const pax = getVenueMealPax(vCard, mealKey);
  const rate = getVenueMealRate(vCard, mealKey);
  return pax * days * rate;
};

const computeVenueMealsTotal = (vCard) => {
  return ['breakfast', 'am_snack', 'lunch', 'pm_snack', 'dinner'].reduce((sum, m) => {
    return sum + computeVenueMealSubtotal(vCard, m);
  }, 0);
};

const toggleBudgetItem = (vCard, itemKey) => {
  if (!vCard.active_items) return;
  vCard.active_items[itemKey] = !vCard.active_items[itemKey];
};

const addOtherItemToVenue = (vCard) => {
  if (!vCard.others) vCard.others = [];
  vCard.others.push({ name: '', amount: '' });
};

const removeOtherItemFromVenue = (vCard, oIdx) => {
  if (vCard.others && vCard.others.length > 1) {
    vCard.others.splice(oIdx, 1);
  }
};

const computeVenueTotal = (vCard) => {
  let total = computeVenueMealsTotal(vCard);
  if (vCard.active_items?.function_room) {
    total += Number(vCard.function_room?.cost) || 0;
  }
  if (vCard.active_items?.accommodation) {
    const p = Number(vCard.accommodation?.pax) || 0;
    const n = Number(vCard.accommodation?.nights) || 0;
    const r = Number(vCard.accommodation?.rate) || 0;
    total += p * n * r;
  }
  if (vCard.active_items?.equipment_rental) {
    total += Number(vCard.equipment_rental?.cost) || 0;
  }
  if (vCard.active_items?.transportation) {
    total += Number(vCard.transportation?.total) || 0;
  }
  if (vCard.active_items?.others && Array.isArray(vCard.others)) {
    total += vCard.others.reduce((s, o) => s + (Number(o.amount) || 0), 0);
  }
  return total;
};

// Activity-Wide Budget State & Methods
const activityBudget = ref({
  active_items: {
    function_room: false,
    accommodation: false,
    equipment_rental: false,
    professional_fee: false,
    tokens: false,
    materials: false,
    transportation: false,
    others: false
  },
  function_room: { description: '', cost: '' },
  accommodation: { description: '', pax: '', nights: 1, rate: 1500 },
  equipment_rental: { description: '', cost: '' },
  professional_fee: { speakers: 1, rate: baselineSettings.value?.pf_honoraria || 2258.25 },
  tokens: { recipients: 1, rate: baselineSettings.value?.tokens || 1000 },
  materials: { pax: '', rate: baselineSettings.value?.materials || 1000 },
  transportation: { description: '', total: '' },
  others: [{ name: '', amount: '' }]
});

const toggleActivityBudgetItem = (itemKey) => {
  if (!activityBudget.value.active_items) return;
  activityBudget.value.active_items[itemKey] = !activityBudget.value.active_items[itemKey];
  if (itemKey === 'materials' && activityBudget.value.active_items.materials) {
    if (!activityBudget.value.materials.pax) {
      activityBudget.value.materials.pax = totalActivityParticipants.value;
    }
  }
};

const addOtherItemToActivity = () => {
  if (!activityBudget.value.others) activityBudget.value.others = [];
  activityBudget.value.others.push({ name: '', amount: '' });
};

const removeOtherItemFromActivity = (oIdx) => {
  if (activityBudget.value.others && activityBudget.value.others.length > 1) {
    activityBudget.value.others.splice(oIdx, 1);
  }
};

const computeActivityBudgetTotal = () => {
  let total = 0;
  const ab = activityBudget.value;
  if (!ab?.active_items) return 0;
  if (ab.active_items.function_room) total += Number(ab.function_room?.cost) || 0;
  if (ab.active_items.accommodation) {
    total += (Number(ab.accommodation?.pax) || 0) * (Number(ab.accommodation?.nights) || 0) * (Number(ab.accommodation?.rate) || 0);
  }
  if (ab.active_items.equipment_rental) total += Number(ab.equipment_rental?.cost) || 0;
  if (ab.active_items.professional_fee) {
    total += (Number(ab.professional_fee?.speakers) || 0) * (Number(ab.professional_fee?.rate) || 0);
  }
  if (ab.active_items.tokens) {
    total += (Number(ab.tokens?.recipients) || 0) * (Number(ab.tokens?.rate) || 0);
  }
  if (ab.active_items.materials) {
    const p = Number(ab.materials?.pax || totalActivityParticipants.value) || 0;
    total += p * (Number(ab.materials?.rate) || 0);
  }
  if (ab.active_items.transportation) total += Number(ab.transportation?.total) || 0;
  if (ab.active_items.others && Array.isArray(ab.others)) {
    total += ab.others.reduce((s, o) => s + (Number(o.amount) || 0), 0);
  }
  return total;
};

// Global Timeline & Activity Totals across all venues
const allSchedulesList = computed(() => {
  const list = [];
  venuesList.value.forEach(v => {
    (v.schedules || []).forEach(s => {
      if (s.date) list.push(s);
    });
  });
  return list.sort((a, b) => new Date(a.date) - new Date(b.date));
});

const computedStartDate = computed(() => {
  return allSchedulesList.value[0]?.date || '';
});

const computedEndDate = computed(() => {
  return allSchedulesList.value[allSchedulesList.value.length - 1]?.date || '';
});

watch(() => computedStartDate.value, (newDate, oldDate) => {
  if (newDate && newDate !== oldDate) {
    const validation = isValidActivityDate(newDate, true);
    if (!validation.valid) {
      document.activeElement?.blur();
      Swal.fire({
        icon: 'warning',
        title: 'Invalid Date',
        text: validation.reason,
        confirmButtonColor: '#b979cc'
      });
      return;
    } else if (validation.isWarning) {
      document.activeElement?.blur();
      Swal.fire({
        icon: 'info',
        title: 'Lead Time Warning',
        text: validation.reason,
        confirmButtonColor: '#b979cc'
      });
    }
  }
});

// Grand total sums all venues + activity-wide budget items
const grandTotalProposedBudget = computed(() => {
  const venuesTotal = venuesList.value.reduce((sum, v) => sum + computeVenueTotal(v), 0);
  return venuesTotal + computeActivityBudgetTotal();
});

// Total target participants sums the peak (max) participants of each venue
const totalActivityParticipants = computed(() => {
  return venuesList.value.reduce((sum, v) => sum + getVenueMaxPax(v), 0);
});

const formatNum = (val) => {
  const n = Number(val);
  if (isNaN(n)) return '0.00';
  return n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

const getVenueDisplayName = (vCard) => {
  if (vCard.venue_id === 'Other') {
    return vCard.custom_venue || 'Custom Venue';
  }
  const match = venues.value.find(v => v.venue_id == vCard.venue_id);
  return match ? match.venue_name : 'Selected Venue';
};

const hasActiveItemAcrossVenues = (itemKey) => {
  return venuesList.value.some(v => v.active_items?.[itemKey]);
};

const getActiveVenuesForItem = (itemKey) => {
  return venuesList.value.filter(v => v.active_items?.[itemKey]);
};

const sumItemAcrossVenues = (itemKey) => {
  return venuesList.value.reduce((sum, v) => {
    if (!v.active_items?.[itemKey]) return sum;
    if (itemKey === 'function_room') return sum + (Number(v.function_room?.cost) || 0);
    if (itemKey === 'accommodation') {
      const p = Number(v.accommodation?.pax) || 0;
      const n = Number(v.accommodation?.nights) || 0;
      const r = Number(v.accommodation?.rate) || 0;
      return sum + (p * n * r);
    }
    if (itemKey === 'equipment_rental') return sum + (Number(v.equipment_rental?.cost) || 0);
    if (itemKey === 'transportation') return sum + (Number(v.transportation?.total) || 0);
    if (itemKey === 'others' && Array.isArray(v.others)) {
      return sum + v.others.reduce((s, o) => s + (Number(o.amount) || 0), 0);
    }
    return sum;
  }, 0);
};

const hasAnyItemForCategory = (catKey) => {
  const inVenues = venuesList.value.some(v => v.active_items?.[catKey]);
  const inActivity = !!activityBudget.value?.active_items?.[catKey];
  return inVenues || inActivity;
};

const sumCategoryTotal = (catKey) => {
  let total = sumItemAcrossVenues(catKey);
  const ab = activityBudget.value;
  if (ab?.active_items?.[catKey]) {
    if (catKey === 'function_room') total += Number(ab.function_room?.cost) || 0;
    else if (catKey === 'accommodation') {
      total += (Number(ab.accommodation?.pax) || 0) * (Number(ab.accommodation?.nights) || 0) * (Number(ab.accommodation?.rate) || 0);
    } else if (catKey === 'equipment_rental') total += Number(ab.equipment_rental?.cost) || 0;
    else if (catKey === 'professional_fee') {
      total += (Number(ab.professional_fee?.speakers) || 0) * (Number(ab.professional_fee?.rate) || 0);
    } else if (catKey === 'tokens') {
      total += (Number(ab.tokens?.recipients) || 0) * (Number(ab.tokens?.rate) || 0);
    } else if (catKey === 'materials') {
      const p = Number(ab.materials?.pax || totalActivityParticipants.value) || 0;
      total += p * (Number(ab.materials?.rate) || 0);
    } else if (catKey === 'transportation') total += Number(ab.transportation?.total) || 0;
    else if (catKey === 'others' && Array.isArray(ab.others)) {
      total += ab.others.reduce((s, o) => s + (Number(o.amount) || 0), 0);
    }
  }
  return total;
};

const submitActivityDesign = async () => {
  // 1. Validate schedules across all venues
  const allSchedules = [];
  for (let i = 0; i < venuesList.value.length; i++) {
    const v = venuesList.value[i];
    const vName = getVenueDisplayName(v);
    if (!v.schedules || v.schedules.length === 0 || v.schedules.some(s => !s.date || !s.start_time || !s.end_time)) {
      Swal.fire({
        icon: 'warning',
        title: 'Incomplete Schedules',
        text: `Please complete all schedule dates and times for Venue #${i + 1} (${vName}).`,
        confirmButtonColor: '#b979cc'
      });
      return;
    }
    v.schedules.forEach(s => {
      allSchedules.push({
        date: s.date,
        start_time: s.start_time,
        end_time: s.end_time,
        meals_and_snacks: s.meals_and_snacks,
        venue_id: v.venue_id,
        venue_name: vName
      });
    });
  }

  allSchedules.sort((a, b) => new Date(a.date) - new Date(b.date));
  form.value.start_date = allSchedules[0]?.date || '';
  form.value.end_date = allSchedules[allSchedules.length - 1]?.date || '';
  form.value.start_time = allSchedules[0]?.start_time || '08:00';
  form.value.end_time = allSchedules[allSchedules.length - 1]?.end_time || '17:00';

  if (allSchedules.length === 0) {
    Swal.fire({ icon: 'warning', title: 'Incomplete Schedules', text: 'Please add at least one schedule date.' });
    return;
  }

  if (adSubmissionLimitEnabled.value) {
    const currentDay = new Date().getDay();
    if (currentDay === 0 || currentDay === 6) {
      Swal.fire({
        icon: 'warning',
        title: 'Submission Not Allowed',
        text: 'Submissions are only allowed from Monday to Friday.',
        confirmButtonColor: '#b979cc'
      });
      return;
    }
  }

  // Validate Gender Issue / GAD Mandate
  if (!form.value.gad_mandate_id) {
    Swal.fire({
      icon: 'warning',
      title: 'Missing Field',
      text: 'Please select a Gender Issue / GAD Mandate before submitting.',
      confirmButtonColor: '#b979cc'
    });
    return;
  }

  // Validate Cause of Gender Issue
  if (!form.value.gender_issue_id || (Array.isArray(form.value.gender_issue_id) && form.value.gender_issue_id.length === 0)) {
    Swal.fire({
      icon: 'warning',
      title: 'Missing Field',
      text: 'Please select a Cause of Gender Issue before submitting.',
      confirmButtonColor: '#b979cc'
    });
    return;
  }

  // Validate activity duration
  const durationValidation = isValidActivityDuration(form.value.start_date, form.value.end_date);
  if (!durationValidation.valid) {
    Swal.fire({
      icon: 'warning',
      title: 'Invalid Duration',
      text: durationValidation.reason,
      confirmButtonColor: '#b979cc'
    });
    return;
  }
  if (durationValidation.isWarning) {
    const result = await Swal.fire({
      icon: 'warning',
      title: 'Long Duration',
      text: durationValidation.reason,
      showCancelButton: true,
      confirmButtonText: 'Yes, proceed',
      cancelButtonText: 'No, cancel',
      confirmButtonColor: '#b979cc'
    });
    if (!result.isConfirmed) {
      return;
    }
  }

  // Validate each venue
  for (let i = 0; i < venuesList.value.length; i++) {
    const v = venuesList.value[i];
    if (!v.venue_id) {
      Swal.fire({
        icon: 'warning',
        title: 'Missing Venue',
        text: `Please select a venue for Venue #${i + 1}.`,
        confirmButtonColor: '#b979cc'
      });
      return;
    }
    if (v.venue_id === 'Other' && !v.custom_venue?.trim()) {
      Swal.fire({
        icon: 'warning',
        title: 'Missing Custom Venue',
        text: `Please specify the custom venue name for Venue #${i + 1}.`,
        confirmButtonColor: '#b979cc'
      });
      return;
    }
    const maxPax = getVenueMaxPax(v);
    if (maxPax <= 0) {
      Swal.fire({
        icon: 'warning',
        title: 'Missing Participants',
        text: `Please specify the participants for Venue #${i + 1} (${getVenueDisplayName(v)}) in the meals or constant pax.`,
        confirmButtonColor: '#b979cc'
      });
      return;
    }
  }

  // Check transportation limit across venues and activity
  const transLimit = Number(baselineSettings.value?.transportation_limit || 20000);
  let totalTransportation = 0;
  for (const v of venuesList.value) {
    if (v.active_items?.transportation) {
      totalTransportation += Number(v.transportation?.total) || 0;
    }
  }
  if (activityBudget.value.active_items?.transportation) {
    totalTransportation += Number(activityBudget.value.transportation?.total) || 0;
  }
  if (totalTransportation > transLimit) {
    Swal.fire({
      icon: 'warning',
      title: 'Transportation Limit Exceeded',
      text: `Total transportation budget across venues and activity (₱${formatNum(totalTransportation)}) cannot exceed the maximum limit of ₱${formatNum(transLimit)}.`,
      confirmButtonColor: '#b979cc'
    });
    return;
  }

  if (!designFile.value) {
    Swal.fire({
      icon: 'warning',
      title: 'Missing Document',
      text: 'Please upload the Activity Design PDF document.',
      confirmButtonColor: '#b979cc'
    });
    return;
  }

  const submitConfirm = await Swal.fire({
    title: 'Confirm Activity Design Submission',
    text: 'Are you sure you want to submit this activity design for review?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, Submit',
    cancelButtonText: 'Cancel',
    confirmButtonColor: '#b979cc'
  });
  if (!submitConfirm.isConfirmed) {
    return;
  }

  Swal.fire({
    title: 'Processing...',
    text: 'Please wait while we submit your design and dispatch email notifications.',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  try {
    const formData = new FormData();
    
    formData.append('form_type', form.value.form_type || form.value.nature);
    formData.append('activity_classification_id', form.value.activity_classification_id);
    formData.append('gad_mandate_id', form.value.gad_mandate_id);
    formData.append('gender_issue_id', form.value.gender_issue_id);
    
    if (form.value.gad_mandate_id && form.value.gad_mandate_id === 'Other') {
      formData.append('custom_gad_mandate', customMandate.value);
    }
    if (form.value.gender_issue_id && form.value.gender_issue_id === 'Other') {
      formData.append('custom_gender_issue', customGenderIssue.value);
    }

    formData.append('activity_title', form.value.activity_title);
    formData.append('start_date', form.value.start_date);
    formData.append('end_date', form.value.end_date);
    formData.append('start_time', form.value.start_time);
    formData.append('end_time', form.value.end_time);
    formData.append('user_id', user.value.id || user.value.user_id);
    
    // Legacy single-venue columns & totals for backward compatibility
    const primaryVenue = venuesList.value[0];
    const allVenueNames = venuesList.value.map(v => getVenueDisplayName(v)).join(', ');
    formData.append('is_inside_bsu', primaryVenue.is_inside_bsu ? '1' : '0');
    if (primaryVenue.venue_id === 'Other') {
      formData.append('venue_id', 'Other');
      formData.append('custom_venue', primaryVenue.custom_venue || '');
    } else {
      formData.append('venue_id', primaryVenue.venue_id);
    }
    formData.append('venue', allVenueNames);
    formData.append('target_participants', totalActivityParticipants.value);
    formData.append('proposed_budget', grandTotalProposedBudget.value);
    formData.append('venues', JSON.stringify(venuesList.value));

    // Build normalized budget items for backend ActivityBudgetItemsModel
    const normalizedBudgetItems = [];

    // 1. Meals & Snacks breakdown per venue
    venuesList.value.forEach(v => {
      const vName = getVenueDisplayName(v);
      const mealParts = [];
      ['breakfast', 'am_snack', 'lunch', 'pm_snack', 'dinner'].forEach(mKey => {
        const days = getVenueMealDays(v, mKey);
        if (days > 0) {
          const pax = getVenueMealPax(v, mKey);
          const rate = getVenueMealRate(v, mKey);
          const mealTitle = mKey.replace('_', ' ').toUpperCase();
          mealParts.push(`${mealTitle}: ${pax} pax x ₱${formatNum(rate)} x ${days} day(s) = ₱${formatNum(pax * days * rate)}`);
        }
      });
      
      const vMealsTotal = computeVenueMealsTotal(v);
      if (vMealsTotal > 0 || mealParts.length > 0) {
        normalizedBudgetItems.push({
          category_id: null,
          item_name: 'Meals and Snacks (AM/PM)',
          sub_item: `${vName}: ${mealParts.join(' | ')}`,
          pax: getVenueMaxPax(v) || null,
          amount: vMealsTotal
        });
      }
    });

    // 2. Additional items per venue
    venuesList.value.forEach(v => {
      const vName = getVenueDisplayName(v);
      if (v.active_items?.function_room && Number(v.function_room?.cost) > 0) {
        normalizedBudgetItems.push({
          category_id: null,
          item_name: 'Function Room/Venue',
          sub_item: `[${vName}]: ${v.function_room.description || 'Function Room'}`,
          pax: null,
          amount: Number(v.function_room.cost) || 0
        });
      }
      if (v.active_items?.accommodation && (Number(v.accommodation?.pax) > 0 || Number(v.accommodation?.rate) > 0)) {
        const p = Number(v.accommodation.pax) || 0;
        const n = Number(v.accommodation.nights) || 0;
        const r = Number(v.accommodation.rate) || 0;
        normalizedBudgetItems.push({
          category_id: null,
          item_name: 'Accommodation',
          sub_item: `[${vName}]: ${v.accommodation.description || ''} (${p} pax x ${n} nights @ ₱${formatNum(r)})`,
          pax: p,
          amount: p * n * r
        });
      }
      if (v.active_items?.equipment_rental && Number(v.equipment_rental?.cost) > 0) {
        normalizedBudgetItems.push({
          category_id: null,
          item_name: 'Equipment Rental',
          sub_item: `[${vName}]: ${v.equipment_rental.description || 'Equipment Rental'}`,
          pax: null,
          amount: Number(v.equipment_rental.cost) || 0
        });
      }
      if (v.active_items?.transportation && Number(v.transportation?.total) > 0) {
        normalizedBudgetItems.push({
          category_id: null,
          item_name: 'Transportation',
          sub_item: `[${vName}]: ${v.transportation.description || 'Transportation'}`,
          pax: null,
          amount: Number(v.transportation.total) || 0
        });
      }
      if (v.active_items?.others && Array.isArray(v.others)) {
        v.others.forEach(o => {
          if (o.name && Number(o.amount) > 0) {
            normalizedBudgetItems.push({
              category_id: null,
              item_name: 'Others',
              sub_item: `[${vName}]: ${o.name}`,
              pax: null,
              amount: Number(o.amount) || 0
            });
          }
        });
      }
    });

    // 3. Activity-wide budget items
    const ab = activityBudget.value;
    if (ab.active_items?.function_room && Number(ab.function_room?.cost) > 0) {
      normalizedBudgetItems.push({
        category_id: null,
        item_name: 'Function Room/Venue',
        sub_item: `Activity-Wide: ${ab.function_room.description || 'Function Room'}`,
        pax: null,
        amount: Number(ab.function_room.cost) || 0
      });
    }
    if (ab.active_items?.accommodation && (Number(ab.accommodation?.pax) > 0 || Number(ab.accommodation?.rate) > 0)) {
      const p = Number(ab.accommodation.pax) || 0;
      const n = Number(ab.accommodation.nights) || 0;
      const r = Number(ab.accommodation.rate) || 0;
      normalizedBudgetItems.push({
        category_id: null,
        item_name: 'Accommodation',
        sub_item: `Activity-Wide: ${ab.accommodation.description || ''} (${p} pax x ${n} nights @ ₱${formatNum(r)})`,
        pax: p,
        amount: p * n * r
      });
    }
    if (ab.active_items?.equipment_rental && Number(ab.equipment_rental?.cost) > 0) {
      normalizedBudgetItems.push({
        category_id: null,
        item_name: 'Equipment Rental',
        sub_item: `Activity-Wide: ${ab.equipment_rental.description || 'Equipment Rental'}`,
        pax: null,
        amount: Number(ab.equipment_rental.cost) || 0
      });
    }
    if (ab.active_items?.professional_fee && (Number(ab.professional_fee?.speakers) > 0 || Number(ab.professional_fee?.rate) > 0)) {
      const s = Number(ab.professional_fee.speakers) || 0;
      const r = Number(ab.professional_fee.rate) || 0;
      normalizedBudgetItems.push({
        category_id: null,
        item_name: 'Professional Fee/Honoraria',
        sub_item: `Activity-Wide: ${s} speaker(s) @ ₱${formatNum(r)}`,
        pax: s,
        amount: s * r
      });
    }
    if (ab.active_items?.tokens && (Number(ab.tokens?.recipients) > 0 || Number(ab.tokens?.rate) > 0)) {
      const rec = Number(ab.tokens.recipients) || 0;
      const r = Number(ab.tokens.rate) || 0;
      normalizedBudgetItems.push({
        category_id: null,
        item_name: 'Token/s',
        sub_item: `Activity-Wide: ${rec} recipient(s) @ ₱${formatNum(r)}`,
        pax: rec,
        amount: rec * r
      });
    }
    if (ab.active_items?.materials && (Number(ab.materials?.pax || totalActivityParticipants.value) > 0 || Number(ab.materials?.rate) > 0)) {
      const p = Number(ab.materials.pax || totalActivityParticipants.value) || 0;
      const r = Number(ab.materials.rate) || 0;
      normalizedBudgetItems.push({
        category_id: null,
        item_name: 'Materials and Supplies',
        sub_item: `Activity-Wide: ${p} pax @ ₱${formatNum(r)}`,
        pax: p,
        amount: p * r
      });
    }
    if (ab.active_items?.transportation && Number(ab.transportation?.total) > 0) {
      normalizedBudgetItems.push({
        category_id: null,
        item_name: 'Transportation',
        sub_item: `Activity-Wide: ${ab.transportation.description || 'Transportation'}`,
        pax: null,
        amount: Number(ab.transportation.total) || 0
      });
    }
    if (ab.active_items?.others && Array.isArray(ab.others)) {
      ab.others.forEach(o => {
        if (o.name && Number(o.amount) > 0) {
          normalizedBudgetItems.push({
            category_id: null,
            item_name: 'Others',
            sub_item: `Activity-Wide: ${o.name}`,
            pax: null,
            amount: Number(o.amount) || 0
          });
        }
      });
    }

    formData.append('budget_items', JSON.stringify(normalizedBudgetItems));
    formData.append('schedules', JSON.stringify(allSchedules));
    formData.append('schedule_type', 'staggered');

    if (designFile.value) {
      formData.append('design_file', designFile.value);
    }

    const response = await api.post('submit-activity-design', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    if (response.data.success) {
      Swal.fire({
        icon: 'success',
        title: 'Submitted Successfully!',
        text: 'Activity Design submitted successfully!',
        confirmButtonColor: '#b979cc'
      }).then(() => {
        router.push('/staff/ad-list');
      });
    }
  } catch (error) {
    console.error('Submission error:', error);
    const errText = error.response?.data?.errors ? JSON.stringify(error.response.data.errors) : 'Failed to submit activity design. Please double check all details.';
    Swal.fire({
      icon: 'error',
      title: 'Submission Failed',
      text: 'Errors: ' + errText,
      confirmButtonColor: '#b979cc'
    });
  }
};

const goBack = () => {
  router.push('/staff/submit');
};

const handleLogout = async () => {
  try {
    await api.get('logout');
    localStorage.removeItem('user');
    router.push('/login');
  } catch (err) {
    localStorage.removeItem('user');
    router.push('/login');
  }
};

onMounted(() => {
  if (!user.value.id || user.value.role !== 'gad_staff') {
    router.push('/login');
  }
  fetchBaselineSettings();
  fetchFormTypes();
  fetchActivityClassifications();
  fetchGADMandates();
  fetchVenues();
  fetchHolidays();
  fetchSystemSettings();
  document.addEventListener('click', closeAllHelp);
});

onUnmounted(() => {
  document.removeEventListener('click', closeAllHelp);
});

const checkTransportationLimit = () => {
  const transItem = form.value.budget_items?.[8];
  const limit = Number(baselineSettings.value?.transportation_limit ?? 20000);
  
  if (transItem && Number(transItem.total) > limit) {
    transItem.total = limit;
    const role = user.value?.role || 'staff';
    Swal.fire({
      icon: 'warning',
      title: 'Limit Exceeded',
      html: `Transportation budget cannot exceed the baseline limit of ₱${limit.toLocaleString('en-US')}.<br><br>
             If you need to request an exemption, please <a href="/${role}/messages" style="color: #b979cc; text-decoration: underline; font-weight: bold;">message the GAD Director/Staff</a>.`,
      confirmButtonColor: '#b979cc'
    });
  }
};

</script>

<style scoped>
.twg-view-wrapper {
  flex: 1;
  width: 100%;
  min-height: 100%;
  display: flex;
  background: transparent;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
}

.text-sm { font-size: 14px; }
.text-3xl { font-size: 26px; }

.form-main-layout {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.main-content-container {
  max-width: 1280px;
  margin-left: auto;
  margin-right: auto;
  width: 100%;
}

.form-header {
  margin-bottom: 32px;
}

.form-main-title {
  font-size: 26px;
  font-weight: 800;
  letter-spacing: -0.025em;
  color: #16213e;
  letter-spacing: -0.02em;
}

.form-description {
  font-size: 14px;
  color: #64748b;
  margin-top: 6px;
}

.form-grid-main {
  display: grid;
  grid-template-columns: minmax(0, 1fr);
  gap: 30px;
}
@media (min-width: 1024px) {
  .form-grid-main {
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
  }
}

.form-column-left, .form-column-right {
  display: flex;
  flex-direction: column;
  gap: 20px;
  min-width: 0;
}

.form-section-divider {
  width: 100%;
  margin: 32px 0 28px 0;
  display: flex;
  align-items: center;
  position: relative;
}

.form-section-divider-line {
  width: 100%;
  height: 1px;
  background: linear-gradient(90deg, rgba(185, 121, 204, 0.05) 0%, rgba(185, 121, 204, 0.35) 30%, rgba(185, 121, 204, 0.35) 70%, rgba(185, 121, 204, 0.05) 100%);
}

.form-column-left {
  border-right: 1px solid rgba(185, 121, 204, 0.2);
  padding-right: 20px;
}
  
.form-section-spacing {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-sub-grid {
  display: grid;
  grid-template-columns: repeat(1, minmax(0, 1fr));
  gap: 20px;
}
@media (min-width: 768px) {
  .form-sub-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

.input-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
  min-width: 0;
}

.form-label {
  display: block;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #b979cc;
}

.form-container-box {
  background: linear-gradient(145deg, #1a1a2e 0%, #16213e 100%);
  border: 1px solid rgba(185, 121, 204, 0.2);
  border-radius: 20px;
  padding: 32px;
  box-shadow: 0 20px 40px rgba(10, 10, 20, 0.4);
}

.custom-input-field {
  width: 100%;
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  padding: 14px 20px;
  font-size: 14px;
  color: #ffffff;
  transition: all 0.2s ease;
}

.custom-input-field:focus {
  background: rgba(255, 255, 255, 0.05);
  border-color: #b979cc;
  outline: none;
  box-shadow: 0 0 0 2px rgba(153, 13, 209, 0.2);
}

.custom-input-field:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  background: rgba(255, 255, 255, 0.02);
}

select.custom-input-field {
  text-overflow: ellipsis;
  white-space: nowrap;
  overflow: hidden;
}

.custom-input-field::placeholder {
  color: #94a3b8;
}

.dark-option {
  background-color: #16213e;
  color: #ffffff;
}

.code-icon-calendar::-webkit-calendar-picker-indicator,
.code-icon-clock::-webkit-calendar-picker-indicator {
  filter: invert(1);
  cursor: pointer;
  opacity: 0.7;
}

.code-icon-calendar::-webkit-calendar-picker-indicator:hover,
.code-icon-clock::-webkit-calendar-picker-indicator:hover {
  opacity: 1;
}

.budget-section {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.budget-table-wrapper {
  overflow-x: auto;
  border-radius: 12px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  background-color: rgba(0, 0, 0, 0.2);
}

.budget-table {
  width: 100%;
  text-align: left;
  border-collapse: collapse;
}

.budget-table-header {
  background-color: rgba(255, 255, 255, 0.05);
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #b979cc;
}

.table-header-cell {
  padding: 10px 16px;
  font-weight: 600;
}

.budget-col-total {
  width: 128px;
}

.budget-table-body {
  border-top: 1px solid rgba(255, 255, 255, 0.05);
}

.budget-item-name {
  padding: 12px 16px;
  color: #cbd5e1;
  line-height: 1.25;
}

.budget-item-input-cell {
  padding: 8px 16px;
}

.budget-input-field {
  background-color: transparent;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  outline: none;
  width: 100%;
  color: #ffffff;
  font-size: 14px;
  padding-top: 4px;
  padding-bottom: 4px;
}
.budget-input-field:focus {
  border-color: #b979cc;
}

.budget-total-field {
  font-weight: 600;
}

.budget-table-footer {
  background-color: rgba(255, 255, 255, 0.05);
}

.grand-total-label {
  padding: 12px 16px;
  font-size: 12px;
  font-weight: 700;
  color: #ffffff;
  text-align: right;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.grand-total-value {
  padding: 12px 16px;
  font-size: 14px;
  font-weight: 700;
  color: #b979cc;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.upload-dropzone {
  border: 2px dashed rgba(185, 121, 204, 0.3);
  background: rgba(185, 121, 204, 0.02);
  border-radius: 14px;
  padding: 30px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
}

.upload-dropzone:hover {
  border-color: #b979cc;
  background: rgba(185, 121, 204, 0.06);
}

.upload-icon {
  font-size: 26px;
  margin-bottom: 8px;
  transition: transform 0.2s ease;
}
.upload-dropzone:hover .upload-icon {
  transform: scale(1.1);
}

.upload-text {
  font-size: 14px;
  font-weight: 600;
  color: #ffffff;
  text-align: center;
  transition: color 0.2s ease;
}
.upload-dropzone:hover .upload-text {
  color: #b979cc;
}

.upload-hint {
  font-size: 12px;
  color: #64748b;
  margin-top: 4px;
}

.uploaded-file-display {
  margin-top: 16px;
  width: 100%;
}

.attachment-section-container {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-top: 10px;
}

.attachment-display-grid {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.attachment-preview-column {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 60px;
  border: 1px dashed rgba(185, 121, 204, 0.15);
  border-radius: 12px;
  padding: 12px;
  background: rgba(185, 121, 204, 0.02);
}

.no-file-uploaded-text {
  color: #94a3b8;
  font-size: 14px;
  text-align: center;
}

.uploaded-file-tag {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.08);
  padding: 8px 14px;
  border-radius: 8px;
  color: #cbd5e1;
  font-size: 12px;
  width: 100%;
}

.uploaded-file-name {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.remove-file-btn {
  color: #f472b6;
  font-weight: 700;
  font-size: 14px;
  margin-left: 8px;
  flex-shrink: 0;
}
.remove-file-btn:hover {
  color: #f43f5e;
}

.form-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 24px;
}

.back-button {
  padding: 12px 24px;
  font-size: 14px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #b979cc;
  border-radius: 12px;
  transition: all 0.2s ease;
}
.back-button:hover {
  background-color: rgba(255, 255, 255, 0.05);
}

.submit-action-btn {
  background: linear-gradient(135deg, #990dd1 0%, #b979cc 100%);
  color: #ffffff;
  padding: 14px 40px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 16px;
  cursor: pointer;
  border: none;
  box-shadow: 0 4px 14px rgba(153, 13, 209, 0.3);
  transition: all 0.3s ease;
}

.submit-action-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(153, 13, 209, 0.45);
  background: linear-gradient(135deg, #b979cc 0%, #990dd1 100%);
}

.resize-none {
  resize: none;
}

.select-arrow-fix {
  appearance: none;
  background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23b979cc' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 20px center;
  background-size: 16px;
}

/* GAD Budget Table Inline Styles */
.budget-sub-controls {
  margin-top: 8px;
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  font-size: 11px;
  color: #94a3b8;
}

.budget-checkbox-label {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  user-select: none;
  color: #cbd5e1;
  font-size: 13px;
  font-weight: 500;
  transition: color 0.2s ease;
}

.budget-checkbox-label:hover {
  color: #ffffff;
}

.budget-checkbox {
  appearance: none;
  -webkit-appearance: none;
  width: 18px;
  height: 18px;
  background-color: rgba(15, 23, 42, 0.3);
  border: 2px solid rgba(185, 121, 204, 0.4);
  border-radius: 5px;
  display: inline-grid;
  place-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  margin: 0;
  position: relative;
}

.budget-checkbox:hover {
  border-color: #b979cc;
  background-color: rgba(185, 121, 204, 0.1);
  box-shadow: 0 0 0 2px rgba(185, 121, 204, 0.2);
}

.budget-checkbox:checked {
  background-color: #b979cc;
  border-color: #b979cc;
  box-shadow: 0 0 8px rgba(185, 121, 204, 0.4);
}

.budget-checkbox:checked::before {
  content: "";
  width: 10px;
  height: 10px;
  background-color: #ffffff;
  clip-path: polygon(14% 44%, 0 58%, 38% 95%, 100% 23%, 86% 9%, 38% 68%);
}

.budget-number-input-label {
  display: flex;
  align-items: center;
  gap: 10px;
  color: #cbd5e1;
  font-size: 13px;
  font-weight: 500;
}

.budget-sub-number-input {
  background-color: rgba(15, 23, 42, 0.3);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 8px;
  padding: 8px 12px;
  width: 90px;
  color: #ffffff;
  font-size: 14px;
  outline: none;
  box-sizing: border-box;
  text-align: center;
  transition: all 0.2s ease;
  font-weight: 600;
}

.budget-sub-number-input:focus {
  border-color: #b979cc;
  background-color: rgba(15, 23, 42, 0.5);
  box-shadow: 0 0 0 2px rgba(185, 121, 204, 0.2);
}

.budget-warning-inline {
  margin-top: 6px;
  font-size: 11px;
  color: #fbbf24;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 4px;
}

.budget-error-inline {
  margin-top: 6px;
  font-size: 11px;
  color: #f43f5e;
  font-weight: bold;
  display: flex;
  align-items: center;
  gap: 4px;
}

/* Others Breakdown Styles */
.others-breakdown-container {
  margin-top: 10px;
  padding: 12px;
  background-color: rgba(0, 0, 0, 0.25);
  border-radius: 10px;
  border: 1px dashed rgba(185, 121, 204, 0.2);
}

.others-breakdown-row {
  display: flex;
  gap: 8px;
  margin-bottom: 8px;
  align-items: center;
}

.others-input-name {
  flex: 1;
  background-color: rgba(26, 26, 46, 0.6);
  border: 1px solid rgba(185, 121, 204, 0.2);
  border-radius: 8px;
  padding: 6px 10px;
  color: #ffffff;
  font-size: 12px;
  outline: none;
  box-sizing: border-box;
}

.others-input-amount {
  width: 110px;
  background-color: rgba(26, 26, 46, 0.6);
  border: 1px solid rgba(185, 121, 204, 0.2);
  border-radius: 8px;
  padding: 6px 10px;
  color: #ffffff;
  font-size: 12px;
  outline: none;
  box-sizing: border-box;
}

.others-input-name:focus,
.others-input-amount:focus {
  border-color: #b979cc;
  box-shadow: 0 0 0 2px rgba(185, 121, 204, 0.15);
}

.btn-remove-other {
  background: transparent;
  border: none;
  color: #f43f5e;
  cursor: pointer;
  font-size: 18px;
  line-height: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 4px;
  transition: color 0.2s;
}

.btn-remove-other:hover {
  color: #fda4af;
}

.btn-add-other {
  background-color: rgba(185, 121, 204, 0.1);
  border: 1px solid rgba(185, 121, 204, 0.25);
  color: #b979cc;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 11px;
  font-weight: 700;
  cursor: pointer;
  margin-top: 4px;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  transition: all 0.2s ease;
  text-transform: uppercase;
  letter-spacing: 0.02em;
}

.btn-add-other:hover {
  background-color: rgba(185, 121, 204, 0.2);
  transform: translateY(-0.5px);
}

.resize-none {
  resize: none;
}

.select-arrow-fix {
  appearance: none;
  background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23b979cc' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 20px center;
  background-size: 16px;
}

.others-total-badge {
  background-color: rgba(185, 121, 204, 0.15);
  border: 1px solid rgba(185, 121, 204, 0.3);
  color: #b979cc;
  padding: 6px 12px;
  border-radius: 8px;
  font-weight: 700;
  font-size: 13px;
  display: inline-block;
}

.budget-groups-container {
  display: flex;
  flex-direction: column;
  gap: 20px;
  margin-top: 10px;
}

.budget-group-card {
  background: rgba(30, 41, 59, 0.45);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 14px;
  padding: 20px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
}

.budget-group-card:hover {
  border-color: rgba(185, 121, 204, 0.3);
  background: rgba(30, 41, 59, 0.6);
  box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
}

.budget-group-header {
  display: flex;
  align-items: center;
  gap: 10px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  padding-bottom: 12px;
  margin-bottom: 16px;
}

.budget-group-icon {
  font-size: 18px;
}

.budget-group-title {
  font-size: 13px;
  font-weight: 700;
  color: #b979cc;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.budget-group-content {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.budget-row-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
  padding-bottom: 16px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.04);
}

.budget-row-item:last-child {
  padding-bottom: 0;
  border-bottom: none;
}

.budget-item-info {
  display: flex;
  flex-direction: column;
  gap: 6px;
  flex-grow: 1;
}

.budget-item-title {
  font-weight: 600;
  color: #f1f5f9;
  font-size: 14px;
}

.budget-item-subtext {
  font-size: 11px;
  color: #64748b;
}

.budget-item-value {
  display: flex;
  align-items: center;
  gap: 6px;
  width: 240px;
  flex-shrink: 0;
  justify-content: flex-end;
}

.budget-currency-symbol {
  color: #64748b;
  font-size: 14px;
  font-weight: 600;
}

.budget-card-input {
  background-color: rgba(15, 23, 42, 0.3);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 8px;
  color: #ffffff;
  font-size: 14px;
  padding: 8px 12px;
  width: 100%;
  text-align: right;
  transition: all 0.2s ease;
  font-weight: 600;
}

.budget-card-input:focus {
  border-color: #b979cc;
  background-color: rgba(15, 23, 42, 0.5);
  box-shadow: 0 0 0 2px rgba(185, 121, 204, 0.2);
  outline: none;
}

.grand-total-banner-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: linear-gradient(135deg, rgba(185, 121, 204, 0.1) 0%, rgba(153, 13, 209, 0.1) 100%);
  border: 1px solid rgba(185, 121, 204, 0.3);
  border-radius: 14px;
  padding: 20px;
  margin-top: 20px;
  box-shadow: 0 4px 15px -3px rgba(185, 121, 204, 0.1);
}

.grand-total-label-banner {
  font-size: 13px;
  font-weight: 700;
  color: #ffffff;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.grand-total-value-banner {
  font-size: 20px;
  font-weight: 800;
  color: #b979cc;
  text-shadow: 0 0 10px rgba(185, 121, 204, 0.2);
}

.label-container {
  display: flex;
  align-items: center;
  gap: 6px;
}

.info-btn {
  background: rgba(185, 121, 204, 0.08);
  border: 1px solid rgba(185, 121, 204, 0.35);
  color: #b979cc;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  font-size: 10px;
  font-weight: bold;
  font-family: serif;
  line-height: 1;
  transition: all 0.25s ease;
}

.info-btn:hover {
  background: #b979cc;
  color: #16213e;
  border-color: #b979cc;
  transform: scale(1.15);
  box-shadow: 0 0 8px rgba(185, 121, 204, 0.4);
}

.info-btn-wrapper {
  position: relative;
  display: inline-flex;
  align-items: center;
}

.simple-popup {
  position: absolute;
  bottom: calc(100% + 10px);
  left: 50%;
  transform: translateX(-50%);
  z-index: 1000;
  background: #1a1a2e;
  border: 1px solid #b979cc;
  border-radius: 8px;
  padding: 10px 14px;
  color: #ffffff;
  font-size: 12px;
  width: 240px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
  line-height: 1.45;
  pointer-events: auto;
  text-transform: none;
  white-space: normal;
}

.simple-popup::after {
  content: "";
  position: absolute;
  top: 100%;
  left: 50%;
  transform: translateX(-50%);
  border-width: 6px;
  border-style: solid;
  border-color: #1a1a2e transparent transparent transparent;
}

.simple-popup::before {
  content: "";
  position: absolute;
  top: 100%;
  left: 50%;
  transform: translateX(-50%);
  border-width: 7px;
  border-style: solid;
  border-color: #b979cc transparent transparent transparent;
  z-index: -1;
}

.fade-pop-enter-active,
.fade-pop-leave-active {
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.fade-pop-enter-from,
.fade-pop-leave-to {
  opacity: 0;
  transform: translate(-50%, 8px) scale(0.95);
}

.fade-pop-enter-to,
.fade-pop-leave-from {
  opacity: 1;
  transform: translate(-50%, 0) scale(1);
}

@media (max-width: 768px) {
  .budget-row-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }
  .budget-item-value {
    width: 100%;
    justify-content: flex-start;
  }
  .budget-sub-controls {
    flex-wrap: wrap;
  }
  .grand-total-banner-card {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }
}
@media (max-width: 768px) {
  .budget-row-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }
  .budget-item-value {
    width: 100%;
    justify-content: flex-start;
  }
  .others-breakdown-row {
    flex-wrap: wrap;
    gap: 8px;
  }
  .others-input-name {
    width: 100%;
    flex: none;
  }
  .others-input-amount {
    flex: 1;
  }
  .budget-sub-controls {
    flex-wrap: wrap;
    width: 100%;
  }
  .budget-item-info {
    width: 100%;
  }
  .budget-card-input {
    width: 100%;
  }
}

/* Multi-Venue Budget Form Styles - High Contrast & Enhanced Readability */
.section-field-label {
  display: block;
  font-size: 13px;
  font-weight: 700;
  color: #cbd5e1;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  margin-bottom: 6px;
}

.venue-input-large {
  font-size: 14px !important;
  min-height: 42px !important;
  padding: 8px 12px !important;
  color: #ffffff !important;
  background: rgba(15, 23, 42, 0.75) !important;
  border: 1px solid rgba(185, 121, 204, 0.35) !important;
  border-radius: 8px !important;
}

.venue-input-large:focus {
  border-color: #c084fc !important;
  box-shadow: 0 0 0 2px rgba(185, 121, 204, 0.25) !important;
}

.venue-input-large::placeholder {
  color: #94a3b8 !important;
}

.venue-budget-card {
  background: rgba(30, 41, 59, 0.6);
  border: 1.5px solid rgba(185, 121, 204, 0.35);
  border-radius: 18px;
  padding: 24px;
  position: relative;
  transition: all 0.3s ease;
  box-shadow: 0 6px 24px -2px rgba(0, 0, 0, 0.4);
}

.venue-budget-card:hover {
  border-color: rgba(185, 121, 204, 0.55);
  background: rgba(30, 41, 59, 0.75);
}

.venue-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid rgba(255, 255, 255, 0.12);
  padding-bottom: 16px;
  margin-bottom: 20px;
  flex-wrap: gap;
  gap: 12px;
}

.venue-header-left {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.venue-header-right {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.venue-index-badge {
  background: rgba(185, 121, 204, 0.25);
  color: #f3e8ff;
  font-size: 13px;
  font-weight: 800;
  padding: 5px 12px;
  border-radius: 9999px;
  border: 1px solid rgba(185, 121, 204, 0.45);
  letter-spacing: 0.05em;
}

.venue-subtotal-badge {
  font-family: monospace;
  font-size: 15px;
  font-weight: 700;
  color: #e9d5ff;
  background: rgba(185, 121, 204, 0.18);
  padding: 6px 14px;
  border-radius: 8px;
  border: 1px solid rgba(185, 121, 204, 0.35);
}

.venue-remove-btn {
  background: rgba(239, 68, 68, 0.18) !important;
  color: #fca5a5 !important;
  border: 1px solid rgba(239, 68, 68, 0.4) !important;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 700;
  padding: 6px 14px;
  cursor: pointer;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.venue-remove-btn:hover {
  background: rgba(239, 68, 68, 0.35) !important;
  color: #ffffff !important;
  border-color: #ef4444 !important;
}

.venue-details-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 18px;
  margin-bottom: 22px;
}
@media (min-width: 640px) {
  .venue-details-grid {
    grid-template-columns: 1.2fr 2fr 1.2fr;
  }
}

.schedule-mode-toggle-group {
  display: flex;
  background: rgba(0, 0, 0, 0.5);
  border-radius: 8px;
  padding: 3px;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.schedule-mode-btn {
  padding: 6px 14px;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  border: none;
  background: transparent;
  color: #cbd5e1;
  transition: all 0.2s ease;
}

.schedule-mode-btn.mode-active {
  background: #b979cc;
  color: #1e1b4b;
  font-weight: 800;
}

.schedule-mode-btn:not(.mode-active):hover {
  color: #ffffff;
  background: rgba(255, 255, 255, 0.05);
}

.meal-check-pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px 12px;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  color: #ffffff;
  cursor: pointer;
  transition: all 0.2s ease;
}

.meal-check-pill:hover {
  background: rgba(185, 121, 204, 0.15);
  border-color: rgba(185, 121, 204, 0.4);
}

.meal-check-pill.pill-disabled {
  opacity: 0.35;
  cursor: not-allowed;
}

.schedule-row-remove-btn {
  background: rgba(239, 68, 68, 0.18) !important;
  color: #f87171 !important;
  border: 1px solid rgba(239, 68, 68, 0.35) !important;
  border-radius: 8px;
  padding: 8px 12px;
  height: 42px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  margin-top: auto;
}

.schedule-row-remove-btn:hover {
  background: rgba(239, 68, 68, 0.35) !important;
  color: #ffffff !important;
  border-color: #ef4444 !important;
}

.btn-add-schedule-date {
  width: 100%;
  margin-top: 10px;
  padding: 12px 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  border-radius: 12px;
  border: 1.5px dashed rgba(185, 121, 204, 0.55);
  background: rgba(185, 121, 204, 0.15);
  color: #ffffff !important;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-add-schedule-date:hover {
  background: rgba(185, 121, 204, 0.28);
  border-color: #b979cc;
  color: #ffffff !important;
  box-shadow: 0 0 14px rgba(185, 121, 204, 0.3);
}

.meals-section-wrapper {
  background: rgba(15, 23, 42, 0.5);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 14px;
  padding: 20px;
  margin-bottom: 20px;
}

.meals-subtotal-header-badge {
  font-family: monospace;
  font-size: 15px;
  font-weight: 800;
  color: #ffffff;
  background: rgba(185, 121, 204, 0.25);
  padding: 6px 14px;
  border-radius: 8px;
  border: 1px solid rgba(185, 121, 204, 0.45);
}

.meals-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 14px;
  margin-top: 12px;
}

.meal-card-item {
  background: rgba(15, 23, 42, 0.75);
  border: 1.5px solid rgba(185, 121, 204, 0.35);
  border-radius: 12px;
  padding: 14px;
  transition: all 0.2s ease;
}

.meal-card-item.meal-active {
  background: rgba(30, 41, 59, 0.7);
  border-color: rgba(185, 121, 204, 0.5);
}

.meal-field-input {
  height: 38px !important;
  padding: 6px 10px !important;
  font-size: 13px !important;
  color: #ffffff !important;
  background: rgba(15, 23, 42, 0.8) !important;
  border: 1px solid rgba(185, 121, 204, 0.3) !important;
  border-radius: 6px !important;
  flex: 1;
}

.quick-add-toolbar {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-top: 10px;
  margin-bottom: 18px;
}

.quick-add-pill {
  background: rgba(15, 23, 42, 0.75);
  border: 1px solid rgba(185, 121, 204, 0.35);
  color: #f1f5f9;
  padding: 8px 16px;
  border-radius: 9999px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: all 0.2s ease;
}

.quick-add-pill:hover {
  background: rgba(185, 121, 204, 0.22);
  border-color: rgba(185, 121, 204, 0.6);
  color: #ffffff;
}

.quick-add-pill.pill-active {
  background: rgba(185, 121, 204, 0.32);
  border-color: #c084fc;
  color: #ffffff;
  font-weight: 700;
  box-shadow: 0 0 10px rgba(185, 121, 204, 0.25);
}

.pill-icon {
  font-weight: 800;
  font-size: 14px;
}

.active-item-card {
  background: rgba(15, 23, 42, 0.75);
  border: 1px solid rgba(185, 121, 204, 0.35);
  border-radius: 14px;
  padding: 18px;
  margin-bottom: 14px;
  transition: all 0.2s ease;
}

.active-item-card:hover {
  border-color: rgba(185, 121, 204, 0.55);
}

.item-remove-badge {
  background: rgba(239, 68, 68, 0.18) !important;
  color: #fca5a5 !important;
  border: 1px solid rgba(239, 68, 68, 0.35) !important;
  border-radius: 6px;
  padding: 4px 10px;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  transition: all 0.2s ease;
}

.item-remove-badge:hover {
  background: rgba(239, 68, 68, 0.35) !important;
  color: #ffffff !important;
  border-color: #ef4444 !important;
}

.item-line-remove-btn {
  background: rgba(239, 68, 68, 0.18) !important;
  color: #f87171 !important;
  border: 1px solid rgba(239, 68, 68, 0.35) !important;
  border-radius: 8px;
  padding: 8px 10px;
  height: 42px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
}

.item-line-remove-btn:hover {
  background: rgba(239, 68, 68, 0.35) !important;
  color: #ffffff !important;
  border-color: #ef4444 !important;
}

.btn-add-line-item {
  margin-top: 8px;
  padding: 8px 16px;
  background: rgba(185, 121, 204, 0.2) !important;
  color: #ffffff !important;
  border: 1px solid rgba(185, 121, 204, 0.5) !important;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: all 0.2s ease;
}

.btn-add-line-item:hover {
  background: rgba(185, 121, 204, 0.35) !important;
  color: #ffffff !important;
  border-color: #c084fc !important;
  box-shadow: 0 0 10px rgba(185, 121, 204, 0.3);
}

.btn-add-venue {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 16px 24px;
  background: rgba(185, 121, 204, 0.15) !important;
  color: #ffffff !important;
  border: 2px dashed rgba(185, 121, 204, 0.55) !important;
  border-radius: 16px;
  font-size: 15px;
  font-weight: 800;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-add-venue:hover {
  background: rgba(185, 121, 204, 0.28) !important;
  border-color: #b979cc !important;
  color: #ffffff !important;
  box-shadow: 0 0 20px rgba(185, 121, 204, 0.3);
}

.official-preview-card {
  background: rgba(20, 20, 35, 0.95);
  border: 1.5px solid rgba(245, 158, 11, 0.45);
  border-radius: 18px;
  padding: 24px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5);
}

/* Constant Pax Bar & Activity-Wide Card Styling */
.constant-pax-tool-bar {
  background: rgba(185, 121, 204, 0.12);
  border: 1.5px solid rgba(185, 121, 204, 0.35);
  border-radius: 12px;
}

.btn-apply-constant-pax {
  background: #b979cc !important;
  color: #1e1b4b !important;
  font-size: 13px !important;
  font-weight: 800 !important;
  padding: 8px 16px !important;
  border-radius: 8px !important;
  border: none !important;
  cursor: pointer !important;
  transition: all 0.2s ease !important;
  white-space: nowrap !important;
}

.btn-apply-constant-pax:hover {
  background: #c084fc !important;
  color: #0f172a !important;
  box-shadow: 0 0 12px rgba(185, 121, 204, 0.5) !important;
}

.activity-wide-card {
  background: rgba(15, 23, 42, 0.7);
  border: 1.5px solid rgba(185, 121, 204, 0.4);
  border-radius: 18px;
}

</style>
