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

      <!-- Room Type Dropdown -->
      <div class="room-type-section">
        <label>Room Type</label>
        <select v-model="selectedRoomType" class="room-dropdown">
          <option value="bedroom">Bedroom</option>
          <option value="living-room">Living Room</option>
          <option value="kitchen">Kitchen</option>
          <option value="dining-room">Dining Room</option>
        </select>
      </div>

      <!-- Room Style Grid -->
      <div class="room-styles">
        <label>Room Style</label>
        <div class="styles-grid">
          <div 
            v-for="style in roomStyles" 
            :key="style.id"
            class="style-item"
            :class="{ active: selectedStyle === style.id }"
            @click="selectedStyle = style.id"
          >
            <img :src="style.image" :alt="style.name" />
            <span>{{ style.name }}</span>
            <div v-if="selectedStyle === style.id" class="check-icon">✓</div>
          </div>
        </div>
      </div>

      <!-- Generate Button -->
      <button class="generate-btn" @click="generateDesign" :disabled="!uploadedImage || isGenerating">
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
  data() {
    return {
      selectedRoomType: 'kitchen',
      selectedStyle: 1,
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
      const selectedStyleName = this.roomStyles.find(s => s.id === this.selectedStyle)?.name || 'Modern';
      
      try {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 120000); // 2 minutes
        
        const response = await fetch('/api/generate-design', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            image: this.uploadedImage,
            roomType: this.selectedRoomType,
            roomStyle: selectedStyleName
          }),
          signal: controller.signal
        });
        
        clearTimeout(timeoutId);
        
        const data = await response.json();
        
        if (data.success) {
          this.generatedImage = data.image_url;
        } else {
          // Fallback: Use a mock generated image for demo
          this.generatedImage = '/images/small/img-' + Math.floor(Math.random() * 9 + 1) + '.jpg';
          console.warn('API failed, using fallback image:', data.error);
        }
      } catch (error) {
        // Fallback for network errors
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

.room-styles label {
  display: block;
  margin-bottom: 12px;
  font-size: 14px;
  font-weight: 500;
}

.styles-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 8px;
  margin-bottom: 30px;
}

.style-item {
  position: relative;
  cursor: pointer;
  text-align: center;
}

.style-item img {
  width: 100%;
  height: 60px;
  object-fit: cover;
  border-radius: 6px;
  border: 2px solid transparent;
}

.style-item.active img {
  border-color: #7c3aed;
}

.style-item span {
  display: block;
  font-size: 11px;
  margin-top: 4px;
  color: #8b8d97;
}

.check-icon {
  position: absolute;
  top: 4px;
  right: 4px;
  background: #7c3aed;
  color: white;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
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
  /* justify-content: center; */
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

.play-btn {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: rgba(255,255,255,0.9);
  border: none;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  font-size: 20px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.top-controls {
  position: absolute;
  top: 20px;
  right: 20px;
  display: flex;
  gap: 10px;
}

.discount-btn, .share-btn, .download-btn {
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 12px;
  font-weight: 500;
}

.discount-btn {
  background: linear-gradient(135deg, #84cc16, #65a30d);
  color: white;
}

.share-btn, .download-btn {
  background: #374151;
  color: white;
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

.image-counter {
  color: white;
  font-size: 14px;
}

.modal-controls {
  display: flex;
  gap: 10px;
}

.modal-btn {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  padding: 8px 12px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
}

.modal-btn:hover {
  background: rgba(255, 255, 255, 0.3);
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