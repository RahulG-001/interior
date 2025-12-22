<template>
  <div class="design-container">
    <!-- Left Sidebar -->
    <div class="sidebar">
      <!-- Images Section -->
      <div class="images-section">
        <h3>Images</h3>
        <div v-if="uploadedImage" class="image-preview">
          <img :src="uploadedImage" alt="Uploaded room" />
          <button class="close-btn" @click="removeImage">×</button>
        </div>
        <div v-else class="upload-area" @click="triggerUpload" @dragover.prevent @drop.prevent="handleDrop">
          <input ref="fileInput" type="file" accept="image/*" @change="handleFileUpload" style="display: none" />
          <div class="upload-content">
            <i class="ri-upload-cloud-2-line"></i>
            <p>Click to upload or drag image here</p>
          </div>
        </div>
      </div>

      <!-- Style/Custom Toggle -->
      <div class="style-toggle">
        <button class="style-btn active">
          <i class="ri-palette-line"></i> Style
        </button>
      </div>

      <!-- Space Type Dropdown -->
      <div class="room-type-section">
        <label>Space Type</label>
        <select v-model="selectedSpaceType" class="room-dropdown">
          <option value="residential">Residential</option>
          <option value="commercial">Commercial</option>
        </select>
      </div>

      <!-- Room Type Dropdown -->
      <div v-if="selectedSpaceType" class="room-type-section">
        <label>Room Type</label>
        <select v-model="selectedRoomType" class="room-dropdown">
          <option value="">Select Room Type</option>
          <template v-if="selectedSpaceType === 'residential'">
            <option value="bedroom">Bedroom</option>
            <option value="living-room">Living Room</option>
            <option value="kitchen">Kitchen</option>
            <option value="master-badroom">Master Bedroom</option>
            <option value="dining-room">Dining Room</option>
            <option value="childrens-bedroom">Children's Bedroom</option>
            <option value="guest-bedroom">Guest Bedroom</option>
            <option value="bathroom-toilet">Bathroom / Toilet</option>
            <option value="balcony-sit-out">Balcony / Sit-out</option>
            <option value="home-office-study-room">Home Office / Study Room</option>
          </template>
          <template v-else>
            <option value="open-plan-office">Open Plan Office</option>
            <option value="executive-office">Executive Office</option>
            <option value="conference-room">Conference Room</option>
            <option value="reception-lobby">Reception / Lobby</option>
            <option value="co-working-space">Co-working Space</option>
            <option value="retail-apparel">Retail – Apparel</option>
            <option value="supermarket">Supermarket</option>
            <option value="showroom">Showroom</option>
            <option value="fine-dining-restaurant">Fine Dining Restaurant</option>
            <option value="cafe-coffee-shop">Cafe / Coffee Shop</option>
            <option value="bar-lounge">Bar / Lounge</option>
            <option value="hotel-lobby">Hotel Lobby</option>
            <option value="hotel-guest-room">Hotel Guest Room</option>
            <option value="clinic-medical-office">Clinic / Medical Office</option>
            <option value="salon-spa">Salon / Spa</option>
            <option value="gym-fitness-center">Gym / Fitness Center</option>
            <option value="bank-branch">Bank Branch</option>
            <option value="classroom-lecture-hall">Classroom / Lecture Hall</option>
          </template>
        </select>
      </div>

      <!-- Style Dropdowns for Residential -->
      <div v-if="selectedSpaceType === 'residential' && selectedRoomType">
        <div v-if="selectedRoomType === 'kitchen'" class="kitchen-styles">
          <label>Kitchen Style</label>
          <select v-model="selectedKitchenStyle" class="room-dropdown">
            <option value="">Select Kitchen Style</option>
            <option v-for="(style, key) in kitchenStyles" :key="key" :value="key">
              {{ style.name }}
            </option>
          </select>
        </div>

        <div v-if="selectedRoomType === 'living-room'" class="kitchen-styles">
          <label>Living Room Style</label>
          <select v-model="selectedLivingRoomStyle" class="room-dropdown">
            <option value="">Select Living Room Style</option>
            <option v-for="(style, key) in livingRoomStyles" :key="key" :value="key">
              {{ style.name }}
            </option>
          </select>
        </div>

        <div v-if="selectedRoomType === 'bedroom'" class="kitchen-styles">
          <label>Bedroom Style</label>
          <select v-model="selectedBedroomStyle" class="room-dropdown">
            <option value="">Select Bedroom Style</option>
            <option v-for="(style, key) in bedroomStyles" :key="key" :value="key">
              {{ style.name }}
            </option>
          </select>
        </div>

        <div v-if="selectedRoomType === 'dining-room'" class="kitchen-styles">
          <label>Dining Room Style</label>
          <select v-model="selectedDiningRoomStyle" class="room-dropdown">
            <option value="">Select Dining Room Style</option>
            <option v-for="(style, key) in diningRoomStyles" :key="key" :value="key">
              {{ style.name }}
            </option>
          </select>
        </div>

        <div v-if="selectedRoomType === 'master-badroom'" class="kitchen-styles">
          <label>Master Bedroom Style</label>
          <select v-model="selectedMasterBedroomStyle" class="room-dropdown">
            <option value="">Select Master Bedroom Style</option>
            <option v-for="(style, key) in masterBedroomStyles" :key="key" :value="key">
              {{ style.name }}
            </option>
          </select>
        </div>

        <div v-if="selectedRoomType === 'childrens-bedroom'" class="kitchen-styles">
          <label>Children's Bedroom Style</label>
          <select v-model="selectedChildrensBedroomStyle" class="room-dropdown">
            <option value="">Select Children's Bedroom Style</option>
            <option v-for="(style, key) in childrensBedroomStyles" :key="key" :value="key">
              {{ style.name }}
            </option>
          </select>
        </div>

        <div v-if="selectedRoomType === 'guest-bedroom'" class="kitchen-styles">
          <label>Guest Bedroom Style</label>
          <select v-model="selectedGuestBedroomStyle" class="room-dropdown">
            <option value="">Select Guest Bedroom Style</option>
            <option v-for="(style, key) in guestBedroomStyles" :key="key" :value="key">
              {{ style.name }}
            </option>
          </select>
        </div>

        <div v-if="selectedRoomType === 'bathroom-toilet'" class="kitchen-styles">
          <label>Bathroom Style</label>
          <select v-model="selectedBathroomStyle" class="room-dropdown">
            <option value="">Select Bathroom Style</option>
            <option v-for="(style, key) in bathroomStyles" :key="key" :value="key">
              {{ style.name }}
            </option>
          </select>
        </div>

        <div v-if="selectedRoomType === 'balcony-sit-out'" class="kitchen-styles">
          <label>Balcony Style</label>
          <select v-model="selectedBalconyStyle" class="room-dropdown">
            <option value="">Select Balcony Style</option>
            <option v-for="(style, key) in balconyStyles" :key="key" :value="key">
              {{ style.name }}
            </option>
          </select>
        </div>

        <div v-if="selectedRoomType === 'home-office-study-room'" class="kitchen-styles">
          <label>Home Office Style</label>
          <select v-model="selectedHomeOfficeStyle" class="room-dropdown">
            <option value="">Select Home Office Style</option>
            <option v-for="(style, key) in homeOfficeStyles" :key="key" :value="key">
              {{ style.name }}
            </option>
          </select>
        </div>
      </div>

      <!-- Style Dropdowns for Commercial -->
      <div v-if="selectedSpaceType === 'commercial' && selectedRoomType">
        <div v-if="selectedRoomType === 'open-plan-office'" class="kitchen-styles">
          <label>Open Plan Office Style</label>
          <select v-model="selectedOpenPlanOfficeStyle" class="room-dropdown">
            <option value="">Select Open Plan Office Style</option>
            <option v-for="(style, key) in openPlanOfficeStyles" :key="key" :value="key">
              {{ style.name }}
            </option>
          </select>
        </div>

        <div v-if="selectedRoomType === 'executive-office'" class="kitchen-styles">
          <label>Executive Office Style</label>
          <select v-model="selectedExecutiveOfficeStyle" class="room-dropdown">
            <option value="">Select Executive Office Style</option>
            <option v-for="(style, key) in executiveOfficeStyles" :key="key" :value="key">
              {{ style.name }}
            </option>
          </select>
        </div>

        <div v-if="selectedRoomType === 'conference-room'" class="kitchen-styles">
          <label>Conference Room Style</label>
          <select v-model="selectedConferenceRoomStyle" class="room-dropdown">
            <option value="">Select Conference Room Style</option>
            <option v-for="(style, key) in conferenceRoomStyles" :key="key" :value="key">
              {{ style.name }}
            </option>
          </select>
        </div>

        <div v-if="selectedRoomType === 'reception-lobby'" class="kitchen-styles">
          <label>Reception Lobby Style</label>
          <select v-model="selectedReceptionLobbyStyle" class="room-dropdown">
            <option value="">Select Reception Lobby Style</option>
            <option v-for="(style, key) in receptionLobbyStyles" :key="key" :value="key">
              {{ style.name }}
            </option>
          </select>
        </div>

        <div v-if="selectedRoomType === 'co-working-space'" class="kitchen-styles">
          <label>Co-working Space Style</label>
          <select v-model="selectedCoWorkingSpaceStyle" class="room-dropdown">
            <option value="">Select Co-working Space Style</option>
            <option v-for="(style, key) in coWorkingSpaceStyles" :key="key" :value="key">
              {{ style.name }}
            </option>
          </select>
        </div>

        <div v-if="selectedRoomType === 'retail-apparel'" class="kitchen-styles">
          <label>Retail Apparel Style</label>
          <select v-model="selectedRetailApparelStyle" class="room-dropdown">
            <option value="">Select Retail Apparel Style</option>
            <option v-for="(style, key) in retailApparelStyles" :key="key" :value="key">
              {{ style.name }}
            </option>
          </select>
        </div>

        <div v-if="selectedRoomType === 'supermarket'" class="kitchen-styles">
          <label>Supermarket Style</label>
          <select v-model="selectedSupermarketStyle" class="room-dropdown">
            <option value="">Select Supermarket Style</option>
            <option v-for="(style, key) in supermarketStyles" :key="key" :value="key">
              {{ style.name }}
            </option>
          </select>
        </div>

        <div v-if="selectedRoomType === 'showroom'" class="kitchen-styles">
          <label>Showroom Style</label>
          <select v-model="selectedShowroomStyle" class="room-dropdown">
            <option value="">Select Showroom Style</option>
            <option v-for="(style, key) in showroomStyles" :key="key" :value="key">
              {{ style.name }}
            </option>
          </select>
        </div>

        <div v-if="selectedRoomType === 'fine-dining-restaurant'" class="kitchen-styles">
          <label>Fine Dining Restaurant Style</label>
          <select v-model="selectedFineDiningRestaurantStyle" class="room-dropdown">
            <option value="">Select Fine Dining Restaurant Style</option>
            <option v-for="(style, key) in fineDiningRestaurantStyles" :key="key" :value="key">
              {{ style.name }}
            </option>
          </select>
        </div>

        <div v-if="selectedRoomType === 'cafe-coffee-shop'" class="kitchen-styles">
          <label>Cafe Coffee Shop Style</label>
          <select v-model="selectedCafeCoffeeShopStyle" class="room-dropdown">
            <option value="">Select Cafe Coffee Shop Style</option>
            <option v-for="(style, key) in cafeCoffeeShopStyles" :key="key" :value="key">
              {{ style.name }}
            </option>
          </select>
        </div>

        <div v-if="selectedRoomType === 'bar-lounge'" class="kitchen-styles">
          <label>Bar Lounge Style</label>
          <select v-model="selectedBarLoungeStyle" class="room-dropdown">
            <option value="">Select Bar Lounge Style</option>
            <option v-for="(style, key) in barLoungeStyles" :key="key" :value="key">
              {{ style.name }}
            </option>
          </select>
        </div>

        <div v-if="selectedRoomType === 'hotel-lobby'" class="kitchen-styles">
          <label>Hotel Lobby Style</label>
          <select v-model="selectedHotelLobbyStyle" class="room-dropdown">
            <option value="">Select Hotel Lobby Style</option>
            <option v-for="(style, key) in hotelLobbyStyles" :key="key" :value="key">
              {{ style.name }}
            </option>
          </select>
        </div>

        <div v-if="selectedRoomType === 'hotel-guest-room'" class="kitchen-styles">
          <label>Hotel Guest Room Style</label>
          <select v-model="selectedHotelGuestRoomStyle" class="room-dropdown">
            <option value="">Select Hotel Guest Room Style</option>
            <option v-for="(style, key) in hotelGuestRoomStyles" :key="key" :value="key">
              {{ style.name }}
            </option>
          </select>
        </div>

        <div v-if="selectedRoomType === 'clinic-medical-office'" class="kitchen-styles">
          <label>Clinic Medical Office Style</label>
          <select v-model="selectedClinicMedicalOfficeStyle" class="room-dropdown">
            <option value="">Select Clinic Medical Office Style</option>
            <option v-for="(style, key) in clinicMedicalOfficeStyles" :key="key" :value="key">
              {{ style.name }}
            </option>
          </select>
        </div>

        <div v-if="selectedRoomType === 'salon-spa'" class="kitchen-styles">
          <label>Salon Spa Style</label>
          <select v-model="selectedSalonSpaStyle" class="room-dropdown">
            <option value="">Select Salon Spa Style</option>
            <option v-for="(style, key) in salonSpaStyles" :key="key" :value="key">
              {{ style.name }}
            </option>
          </select>
        </div>

        <div v-if="selectedRoomType === 'gym-fitness-center'" class="kitchen-styles">
          <label>Gym Fitness Center Style</label>
          <select v-model="selectedGymFitnessCenterStyle" class="room-dropdown">
            <option value="">Select Gym Fitness Center Style</option>
            <option v-for="(style, key) in gymFitnessCenterStyles" :key="key" :value="key">
              {{ style.name }}
            </option>
          </select>
        </div>

        <div v-if="selectedRoomType === 'bank-branch'" class="kitchen-styles">
          <label>Bank Branch Style</label>
          <select v-model="selectedBankBranchStyle" class="room-dropdown">
            <option value="">Select Bank Branch Style</option>
            <option v-for="(style, key) in bankBranchStyles" :key="key" :value="key">
              {{ style.name }}
            </option>
          </select>
        </div>

        <div v-if="selectedRoomType === 'classroom-lecture-hall'" class="kitchen-styles">
          <label>Classroom Style</label>
          <select v-model="selectedClassroomLectureHallStyle" class="room-dropdown">
            <option value="">Select Classroom Style</option>
            <option v-for="(style, key) in classroomLectureHallStyles" :key="key" :value="key">
              {{ style.name }}
            </option>
          </select>
        </div>
      </div>

      <!-- Generate Button -->
      <button class="generate-btn" @click="generateDesign" :disabled="!uploadedImage || !selectedSpaceType || !selectedRoomType || isGenerating">
        <span v-if="isGenerating">Generating...</span>
        <span v-else>Generate →</span>  
      </button>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
      <div class="image-display">
        <div v-if="!uploadedImage" class="placeholder-text">
          <p>First upload an image.</p>
        </div>
        <div v-else-if="isGenerating" class="loading-state">
          <div class="spinner"></div>
          <p>Generating your design...</p>
        </div>
        <div v-else-if="generatedImage" class="comparison-view">
          <div class="image-section">
            <h4>Original</h4>
            <img :src="uploadedImage" alt="Original room" />
          </div>
          <div class="image-section">
            <h4>Generated</h4>
            <img :src="generatedImage" alt="Generated design" @click="showFullscreen = true" style="cursor: pointer;" />
          </div>
        </div>
        <div v-else class="single-image">
          <img :src="uploadedImage" alt="Uploaded room" />
        </div>
      </div>
    </div>

    <!-- Fullscreen Modal -->
    <div v-if="showFullscreen" class="fullscreen-modal" @click="showFullscreen = false">
      <div class="modal-content" @click.stop>
        <button class="close-modal" @click="showFullscreen = false">×</button>
        <div class="fullscreen-header">
        </div>
        <img :src="generatedImage" alt="Generated design fullscreen" />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DesignPage',
  props: {
    kitchenStyles: {
      type: Object,
      default: () => ({})
    },
    bedroomStyles: {
      type: Object,
      default: () => ({})
    },
    livingRoomStyles: {
      type: Object,
      default: () => ({})
    },
    diningRoomStyles: {
      type: Object,
      default: () => ({})
    },
    masterBedroomStyles: {
      type: Object,
      default: () => ({})
    },
    childrensBedroomStyles: {
      type: Object,
      default: () => ({})
    },
    guestBedroomStyles: {
      type: Object,
      default: () => ({})
    },
    bathroomStyles: {
      type: Object,
      default: () => ({})
    },
    balconyStyles: {
      type: Object,
      default: () => ({})
    },
    homeOfficeStyles: {
      type: Object,
      default: () => ({})
    },
    // Commercial space styles
    openPlanOfficeStyles: {
      type: Object,
      default: () => ({})
    },
    executiveOfficeStyles: {
      type: Object,
      default: () => ({})
    },
    conferenceRoomStyles: {
      type: Object,
      default: () => ({})
    },
    receptionLobbyStyles: {
      type: Object,
      default: () => ({})
    },
    coWorkingSpaceStyles: {
      type: Object,
      default: () => ({})
    },
    retailApparelStyles: {
      type: Object,
      default: () => ({})
    },
    supermarketStyles: {
      type: Object,
      default: () => ({})
    },
    showroomStyles: {
      type: Object,
      default: () => ({})
    },
    fineDiningRestaurantStyles: {
      type: Object,
      default: () => ({})
    },
    cafeCoffeeShopStyles: {
      type: Object,
      default: () => ({})
    },
    barLoungeStyles: {
      type: Object,
      default: () => ({})
    },
    hotelLobbyStyles: {
      type: Object,
      default: () => ({})
    },
    hotelGuestRoomStyles: {
      type: Object,
      default: () => ({})
    },
    clinicMedicalOfficeStyles: {
      type: Object,
      default: () => ({})
    },
    salonSpaStyles: {
      type: Object,
      default: () => ({})
    },
    gymFitnessCenterStyles: {
      type: Object,
      default: () => ({})
    },
    bankBranchStyles: {
      type: Object,
      default: () => ({})
    },
    classroomLectureHallStyles: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      selectedSpaceType: 'residential',
      selectedRoomType: '',
      selectedStyle: 1,
      selectedKitchenStyle: '',
      selectedLivingRoomStyle: '',
      selectedBedroomStyle: '',
      selectedDiningRoomStyle: '',
      selectedMasterBedroomStyle: '',
      selectedChildrensBedroomStyle: '',
      selectedGuestBedroomStyle: '',
      selectedBathroomStyle: '',
      selectedBalconyStyle: '',
      selectedHomeOfficeStyle: '',
      // Commercial space selections
      selectedOpenPlanOfficeStyle: '',
      selectedExecutiveOfficeStyle: '',
      selectedConferenceRoomStyle: '',
      selectedReceptionLobbyStyle: '',
      selectedCoWorkingSpaceStyle: '',
      selectedRetailApparelStyle: '',
      selectedSupermarketStyle: '',
      selectedShowroomStyle: '',
      selectedFineDiningRestaurantStyle: '',
      selectedCafeCoffeeShopStyle: '',
      selectedBarLoungeStyle: '',
      selectedHotelLobbyStyle: '',
      selectedHotelGuestRoomStyle: '',
      selectedClinicMedicalOfficeStyle: '',
      selectedSalonSpaStyle: '',
      selectedGymFitnessCenterStyle: '',
      selectedBankBranchStyle: '',
      selectedClassroomLectureHallStyle: '',
      uploadedImage: null,
      generatedImage: null,
      showFullscreen: false,
      isGenerating: false,
      mainImage: '/images/small/img-1.jpg',
      roomStyles: [
        { id: 1, name: 'bohemian', image: '/images/design/bohemian.webp' },
        { id: 2, name: 'Scandinavian', image: '/images/design/christmas.webp' },
        { id: 3, name: 'Christmas', image: '/images/design/cn-style.jpeg' },
        { id: 4, name: 'Industrial', image: '/images/design/eu-style.jpeg' },
        { id: 5, name: 'Bohemian', image: '/images/design/industrial (1).webp' },
        { id: 6, name: 'Luxury', image: '/images/design/industrial.webp' },
        { id: 7, name: 'New Chinese', image: '/images/design/luxury.webp' },
        { id: 8, name: 'European', image: '/images/design/med-style.jpeg' },
        { id: 9, name: 'Mediterranean', image: '/images/design/scandinavian.webp' }
      ]
    }
  },
  methods: {
    triggerUpload() {
      this.$refs.fileInput.click();
    },
    handleFileUpload(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          this.uploadedImage = e.target.result;
          this.mainImage = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    },
    handleDrop(event) {
      const files = event.dataTransfer.files;
      if (files.length > 0) {
        const file = files[0];
        if (file.type.startsWith('image/')) {
          const reader = new FileReader();
          reader.onload = (e) => {
            this.uploadedImage = e.target.result;
            this.mainImage = e.target.result;
          };
          reader.readAsDataURL(file);
        }
      }
    },
    removeImage() {
      this.uploadedImage = null;
      this.generatedImage = null;
      this.mainImage = '/images/small/img-1.jpg';
    },
    async generateDesign() {
      if (!this.uploadedImage) return;
      
      this.isGenerating = true;
      let selectedStyleName = 'Modern';
      
      // Get style name based on room type and selected style
      if (this.selectedSpaceType === 'residential') {
        if (this.selectedRoomType === 'kitchen' && this.selectedKitchenStyle) {
          selectedStyleName = this.kitchenStyles[this.selectedKitchenStyle]?.name || 'Modern';
        } else if (this.selectedRoomType === 'living-room' && this.selectedLivingRoomStyle) {
          selectedStyleName = this.livingRoomStyles[this.selectedLivingRoomStyle]?.name || 'Modern';
        } else if (this.selectedRoomType === 'bedroom' && this.selectedBedroomStyle) {
          selectedStyleName = this.bedroomStyles[this.selectedBedroomStyle]?.name || 'Modern';
        } else if (this.selectedRoomType === 'dining-room' && this.selectedDiningRoomStyle) {
          selectedStyleName = this.diningRoomStyles[this.selectedDiningRoomStyle]?.name || 'Modern';
        } else if (this.selectedRoomType === 'master-badroom' && this.selectedMasterBedroomStyle) {
          selectedStyleName = this.masterBedroomStyles[this.selectedMasterBedroomStyle]?.name || 'Modern';
        } else if (this.selectedRoomType === 'childrens-bedroom' && this.selectedChildrensBedroomStyle) {
          selectedStyleName = this.childrensBedroomStyles[this.selectedChildrensBedroomStyle]?.name || 'Modern';
        } else if (this.selectedRoomType === 'guest-bedroom' && this.selectedGuestBedroomStyle) {
          selectedStyleName = this.guestBedroomStyles[this.selectedGuestBedroomStyle]?.name || 'Modern';
        } else if (this.selectedRoomType === 'bathroom-toilet' && this.selectedBathroomStyle) {
          selectedStyleName = this.bathroomStyles[this.selectedBathroomStyle]?.name || 'Modern';
        } else if (this.selectedRoomType === 'balcony-sit-out' && this.selectedBalconyStyle) {
          selectedStyleName = this.balconyStyles[this.selectedBalconyStyle]?.name || 'Modern';
        } else if (this.selectedRoomType === 'home-office-study-room' && this.selectedHomeOfficeStyle) {
          selectedStyleName = this.homeOfficeStyles[this.selectedHomeOfficeStyle]?.name || 'Modern';
        }
      } else if (this.selectedSpaceType === 'commercial') {
        if (this.selectedRoomType === 'open-plan-office' && this.selectedOpenPlanOfficeStyle) {
          selectedStyleName = this.openPlanOfficeStyles[this.selectedOpenPlanOfficeStyle]?.name || 'Modern';
        } else if (this.selectedRoomType === 'executive-office' && this.selectedExecutiveOfficeStyle) {
          selectedStyleName = this.executiveOfficeStyles[this.selectedExecutiveOfficeStyle]?.name || 'Modern';
        } else if (this.selectedRoomType === 'conference-room' && this.selectedConferenceRoomStyle) {
          selectedStyleName = this.conferenceRoomStyles[this.selectedConferenceRoomStyle]?.name || 'Modern';
        } else if (this.selectedRoomType === 'reception-lobby' && this.selectedReceptionLobbyStyle) {
          selectedStyleName = this.receptionLobbyStyles[this.selectedReceptionLobbyStyle]?.name || 'Modern';
        } else if (this.selectedRoomType === 'co-working-space' && this.selectedCoWorkingSpaceStyle) {
          selectedStyleName = this.coWorkingSpaceStyles[this.selectedCoWorkingSpaceStyle]?.name || 'Modern';
        } else if (this.selectedRoomType === 'retail-apparel' && this.selectedRetailApparelStyle) {
          selectedStyleName = this.retailApparelStyles[this.selectedRetailApparelStyle]?.name || 'Modern';
        } else if (this.selectedRoomType === 'supermarket' && this.selectedSupermarketStyle) {
          selectedStyleName = this.supermarketStyles[this.selectedSupermarketStyle]?.name || 'Modern';
        } else if (this.selectedRoomType === 'showroom' && this.selectedShowroomStyle) {
          selectedStyleName = this.showroomStyles[this.selectedShowroomStyle]?.name || 'Modern';
        } else if (this.selectedRoomType === 'fine-dining-restaurant' && this.selectedFineDiningRestaurantStyle) {
          selectedStyleName = this.fineDiningRestaurantStyles[this.selectedFineDiningRestaurantStyle]?.name || 'Modern';
        } else if (this.selectedRoomType === 'cafe-coffee-shop' && this.selectedCafeCoffeeShopStyle) {
          selectedStyleName = this.cafeCoffeeShopStyles[this.selectedCafeCoffeeShopStyle]?.name || 'Modern';
        } else if (this.selectedRoomType === 'bar-lounge' && this.selectedBarLoungeStyle) {
          selectedStyleName = this.barLoungeStyles[this.selectedBarLoungeStyle]?.name || 'Modern';
        } else if (this.selectedRoomType === 'hotel-lobby' && this.selectedHotelLobbyStyle) {
          selectedStyleName = this.hotelLobbyStyles[this.selectedHotelLobbyStyle]?.name || 'Modern';
        } else if (this.selectedRoomType === 'hotel-guest-room' && this.selectedHotelGuestRoomStyle) {
          selectedStyleName = this.hotelGuestRoomStyles[this.selectedHotelGuestRoomStyle]?.name || 'Modern';
        } else if (this.selectedRoomType === 'clinic-medical-office' && this.selectedClinicMedicalOfficeStyle) {
          selectedStyleName = this.clinicMedicalOfficeStyles[this.selectedClinicMedicalOfficeStyle]?.name || 'Modern';
        } else if (this.selectedRoomType === 'salon-spa' && this.selectedSalonSpaStyle) {
          selectedStyleName = this.salonSpaStyles[this.selectedSalonSpaStyle]?.name || 'Modern';
        } else if (this.selectedRoomType === 'gym-fitness-center' && this.selectedGymFitnessCenterStyle) {
          selectedStyleName = this.gymFitnessCenterStyles[this.selectedGymFitnessCenterStyle]?.name || 'Modern';
        } else if (this.selectedRoomType === 'bank-branch' && this.selectedBankBranchStyle) {
          selectedStyleName = this.bankBranchStyles[this.selectedBankBranchStyle]?.name || 'Modern';
        } else if (this.selectedRoomType === 'classroom-lecture-hall' && this.selectedClassroomLectureHallStyle) {
          selectedStyleName = this.classroomLectureHallStyles[this.selectedClassroomLectureHallStyle]?.name || 'Modern';
        }
      }
      
      try {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 120000);
        
        const requestBody = {
          image: this.uploadedImage,
          roomType: this.selectedRoomType,
          spaceType: this.selectedSpaceType,
          style: selectedStyleName
        };
        
        const response = await fetch('/api/generate-design', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify(requestBody),
          signal: controller.signal
        });
        
        clearTimeout(timeoutId);
        
        const data = await response.json();
        
        if (data.success) {
          this.generatedImage = data.image_url;
        } else {
          this.generatedImage = '/images/small/img-' + Math.floor(Math.random() * 9 + 1) + '.jpg';
          console.warn('API failed, using fallback image:', data.error);
        }
      } catch (error) {
        this.generatedImage = '/images/small/img-' + Math.floor(Math.random() * 9 + 1) + '.jpg';
        console.warn('Network error, using fallback image:', error.message);
      } finally {
        this.isGenerating = false;
      }
    }
  }
}
</script>

<style scoped>
.design-container {
  display: flex;
  height: 100vh;
  background: #1a1d29;
  color: white;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.sidebar {
  width: 300px;
  background: #252836;
  padding: 20px;
  overflow-y: auto;
}

.images-section h3 {
  margin: 0 0 15px 0;
  font-size: 16px;
  font-weight: 500;
}

.image-preview {
  position: relative;
  margin-bottom: 20px;
}

.image-preview img {
  width: 100%;
  height: 120px;
  object-fit: cover;
  border-radius: 8px;
}

.close-btn {
  position: absolute;
  top: 8px;
  right: 8px;
  background: rgba(0,0,0,0.5);
  border: none;
  color: white;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  cursor: pointer;
}

.upload-area {
  border: 2px dashed #3a3d4a;
  border-radius: 8px;
  padding: 30px 20px;
  text-align: center;
  cursor: pointer;
  transition: border-color 0.3s;
  margin-bottom: 20px;
}

.upload-area:hover {
  border-color: #7c3aed;
}

.upload-content i {
  font-size: 32px;
  color: #8b8d97;
  margin-bottom: 10px;
  display: block;
}

.upload-content p {
  margin: 0;
  color: #8b8d97;
  font-size: 14px;
}

.style-toggle {
  display: flex;
  margin-bottom: 20px;
  background: #1a1d29;
  border-radius: 8px;
  padding: 4px;
}

.style-btn, .custom-btn {
  flex: 1;
  padding: 8px 12px;
  border: none;
  background: transparent;
  color: #8b8d97;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 14px;
}

.style-btn.active {
  background: #7c3aed;
  color: white;
}

.room-type-section {
  margin-bottom: 20px;
}

.room-type-section label {
  display: block;
  margin-bottom: 8px;
  font-size: 14px;
  font-weight: 500;
}

.room-dropdown {
  width: 100%;
  padding: 10px;
  background: #1a1d29;
  border: 1px solid #3a3d4a;
  border-radius: 6px;
  color: white;
  font-size: 14px;
}

.kitchen-styles {
  margin-bottom: 20px;
}

.kitchen-styles label {
  display: block;
  margin-bottom: 8px;
  font-size: 14px;
  font-weight: 500;
}

.generate-btn {
  width: 100%;
  padding: 12px;
  background: linear-gradient(135deg, #84cc16, #65a30d);
  border: none;
  border-radius: 8px;
  color: white;
  font-weight: 500;
  cursor: pointer;
  font-size: 14px;
}

.main-content {
  flex: 1;
  display: flex;
  align-items: center;
  padding: 20px;
  overflow: auto;
}

.image-display {
  width: 100%;
  max-width: 1200px;
  min-height: 400px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.placeholder-text {
  text-align: center;
  color: #8b8d97;
  font-size: 18px;
  background: #252836;
  padding: 60px 40px;
  border-radius: 12px;
  border: 2px dashed #3a3d4a;
}

.loading-state {
  text-align: center;
  color: #8b8d97;
  background: #252836;
  padding: 60px 40px;
  border-radius: 12px;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #3a3d4a;
  border-top: 4px solid #7c3aed;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 20px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.comparison-view {
  display: flex;
  gap: 30px;
  width: 100%;
  align-items: flex-start;
}

.image-section {
  flex: 1;
  background: #252836;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.image-section h4 {
  margin: 0 0 15px 0;
  color: #ffffff;
  font-size: 16px;
  font-weight: 600;
  text-align: center;
  padding-bottom: 10px;
  border-bottom: 2px solid #3a3d4a;
}

.image-section img {
  width: 100%;
  height: 300px;
  object-fit: cover;
  border-radius: 8px;
  border: 2px solid #3a3d4a;
  transition: transform 0.3s ease;
}

.image-section img:hover {
  transform: scale(1.02);
  border-color: #7c3aed;
}

.single-image {
  width: 100%;
  max-width: 600px;
  background: #252836;
  border-radius: 12px;
  padding: 20px;
  text-align: center;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.single-image img {
  width: 100%;
  height: auto;
  max-height: 500px;
  object-fit: cover;
  border-radius: 8px;
  border: 2px solid #3a3d4a;
}

.generate-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.fullscreen-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.95);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  position: relative;
  max-width: 90vw;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
}

.fullscreen-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 0;
  margin-bottom: 10px;
}

.close-modal {
  position: absolute;
  top: -40px;
  right: 0;
  background: none;
  border: none;
  color: white;
  font-size: 30px;
  cursor: pointer;
  z-index: 1001;
}

.close-modal:hover {
  color: #ccc;
}

.modal-content img {
  max-width: 100%;
  max-height: 80vh;
  object-fit: contain;
  border-radius: 8px;
}
</style>