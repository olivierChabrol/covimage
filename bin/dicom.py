#!/usr/bin/env python
# coding: utf-8

# ## Aperçu des scans

# In[ ]:


import cv2
import numpy as np
import pydicom
from glob import glob
import natsort
import matplotlib.pyplot as plt
import os
from skimage import exposure
from skimage.io import imsave


def normalize_minmax(data):
    """
    Normalize contrast across volume
    """
    _min = np.float(np.min(data))
    _max = np.float(np.max(data))
    if (_max-_min)!=0:
        img = (data - _min) / (_max-_min)
    else:
        img = np.zeros_like(data)            
    return img

def imcrop(img, bbox):
    x1, y1, x2, y2 = bbox
    if x1 < 0 or y1 < 0 or x2 > img.shape[1] or y2 > img.shape[0]:
        img, x1, x2, y1, y2 = pad_img_to_fit_bbox(img, x1, x2, y1, y2)
    return img[y1:y2, x1:x2]

def pad_img_to_fit_bbox(img, x1, x2, y1, y2):
    img = cv2.copyMakeBorder(img, - min(0, y1), max(y2 - img.shape[0], 0),
                            -min(0, x1), max(x2 - img.shape[1], 0),cv2.BORDER_REPLICATE)
    y2 += -min(0, y1)
    y1 += -min(0, y1)
    x2 += -min(0, x1)
    x1 += -min(0, x1)
    return img, x1, x2, y1, y2



def Dicom_to_png(root, path_save):
    
    path_dcms = natsort.natsorted(glob(root+'IM*'))
    dcms = [pydicom.read_file(path) for path in path_dcms]
    imgs_dcms = [dcm.pixel_array for dcm in dcms]
    print("Résolution des images: %s x %s"%imgs_dcms[0].shape)
    print("Nombre de coupes: %s"%len(imgs_dcms))
#    cX, cY = 255, 255
#    h = 384
#    bbox = int(cX- h/2),int(cY - h/2),int(cX + h/2),int(cY + h/2)
    patient_name = root.split('\\')[-2]
    print(patient_name)
    if not os.path.exists(path_save + patient_name + '\\'):
        os.mkdir(path_save + patient_name + '\\')
        
    for n in range(len(imgs_dcms)):
        img0 = imgs_dcms[n]
        img0[img0==-2000]=0
        img = (normalize_minmax(img0)*65355).astype('uint16')
 #       array = imcrop(img, bbox)
        imsave(path_save + patient_name +'\\IM' + str(n) + '.png', img)
      #  imsave(root +'templates_adapthist/'+'IM_' + str(n) + '.png', normalize_minmax(imgs_dcms[n]))



ROOT = '..\\DATA\\COVID19\\'
path_save = '..\\DATA\\COVID19_16bits\\'
PATIENTS = glob('..\\DATA\\COVID19\\*')



for patient in PATIENTS:
    Dicom_to_png(patient+'\\', path_save)
    



img = pydicom.read_file(ROOT+'\\665617-31796\\IM146').pixel_array



norm = normalize_minmax(img)



img = np.clip(img, -1250, 250)



plt.figure(figsize=(12,12))
plt.imshow(img)

