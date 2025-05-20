// Fungsi untuk menginisialisasi face-api.js dengan model lokal
window.faceApiInit = async () => {
  try {
    console.log('Preloading face-api models');
    
    // Gunakan model SSD MobileNet yang lebih akurat daripada TinyFaceDetector
    console.log('Loading SSD MobileNet model...');
    await faceapi.loadSsdMobilenetv1Model('/models');
    console.log('SSD MobileNet loaded successfully');
    
    console.log('Loading face landmark model...');
    await faceapi.loadFaceLandmarkModel('/models');
    console.log('Face landmark model loaded successfully');
    
    // Cek apakah menggunakan API style baru atau lama
    console.log('Loading face recognition model...');
    try {
      // Coba metode API modern terlebih dahulu (di halaman admin)
      if (typeof faceapi.loadFaceRecognitionNet === 'function') {
        await faceapi.loadFaceRecognitionNet('/models');
        console.log('Face recognition model loaded successfully (modern API)');
      } 
      // Coba metode API lama/legacy (di halaman attendance)
      else if (typeof faceapi.nets !== 'undefined' && typeof faceapi.nets.faceRecognitionNet !== 'undefined') {
        await faceapi.nets.faceRecognitionNet.loadFromUri('/models');
        console.log('Face recognition model loaded successfully (legacy API)');
      }
      // Jika tidak ada keduanya, lewati saja
      else {
        console.log('Face recognition model not available in this version, skipping');
      }
    } catch (recError) {
      console.warn('Could not load face recognition model, skipping:', recError);
    }
    
    // Coba load model ekspresi wajah
    console.log('Loading face expression model...');
    try {
      // Coba metode API modern terlebih dahulu
      if (typeof faceapi.loadFaceExpressionModel === 'function') {
        await faceapi.loadFaceExpressionModel('/models');
        console.log('Face expression model loaded successfully (modern API)');
      }
      // Coba metode API lama/legacy
      else if (typeof faceapi.nets !== 'undefined' && typeof faceapi.nets.faceExpressionNet !== 'undefined') {
        await faceapi.nets.faceExpressionNet.loadFromUri('/models');
        console.log('Face expression model loaded successfully (legacy API)');
      }
      // Jika tidak ada keduanya, lewati saja
      else {
        console.log('Face expression model not available in this version, skipping');
      }
    } catch (exprError) {
      console.warn('Could not load face expression model, skipping:', exprError);
    }
    
    // Coba load model umur dan gender
    console.log('Loading age gender model...');
    try {
      // Coba metode API modern terlebih dahulu
      if (typeof faceapi.loadAgeGenderModel === 'function') {
        await faceapi.loadAgeGenderModel('/models');
        console.log('Age gender model loaded successfully (modern API)');
      }
      // Coba metode API lama/legacy
      else if (typeof faceapi.nets !== 'undefined' && typeof faceapi.nets.ageGenderNet !== 'undefined') {
        await faceapi.nets.ageGenderNet.loadFromUri('/models');
        console.log('Age gender model loaded successfully (legacy API)');
      }
      // Jika tidak ada keduanya, lewati saja
      else {
        console.log('Age gender model not available in this version, skipping');
      }
    } catch (ageError) {
      console.warn('Could not load age gender model, skipping:', ageError);
    }
    
    console.log('All available models loaded successfully');
    return true;
  } catch (e) {
    console.error('Error loading face-api models:', e);
    return false;
  }
};
