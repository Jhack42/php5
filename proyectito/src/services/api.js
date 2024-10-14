import { USE_MOCK_API } from '@/config';
import { getFacultades as getRealFacultades } from './facultades';
import { getFacultadesMock } from '@/mocks/facultades';

// Función centralizada para obtener las facultades
export const getFacultades = async () => {
  if (USE_MOCK_API) {
    // Usar la API emulada
    return getFacultadesMock();
  } else {
    // Usar la API real
    return getRealFacultades();
  }
};
