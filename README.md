# Roll Scheduling & Bag Calculation Guide

This guide provides a structured approach for roll scheduling, determining printing cylinders, bag calculations, and roll size determination based on different bag types.

---

## 1. Roll Scheduling for Printing  
- Scheduling should be based on **cylinder size**, **delivery date**, and **availability of cutting machines**.  

---

## 2. Determining the Printing Cylinder for a Roll  
- Rolls should be grouped based on **same size categories**.  
- The bag size is determined using the following formulas:  
  - **D Cut, Loop, and Box Bags:** Width + Gusset (`W + G`)  
  - **U Cut Bags:** Length  

---

## 3. Calculating the Number of Bags from a Roll  

### a) Based on Length  
- **D Cut & Loop Bags:**  
  ```
  (Length of the Roll x 39.37) / Width of the Bag
  ```
- **Box Bags:**  
  ```
  (Length of the Roll x 39.37) / (Width of the Bag + Gusset of the Bag)
  ```
- **U Cut Bags:**  
  ```
  (Length of the Roll x 39.37) / Height of the Bag
  ```

### b) Based on Net Weight  
- **D Cut & Loop Bags:**  
  ```
  (Net Weight of the Roll x 1550) / (Width of the Bag x Roll Size x GSM of the Roll)
  ```
- **Box Bags:**  
  ```
  (Net Weight of the Roll x 1550) / ((Width of the Bag + Gusset of the Bag) x Roll Size x GSM of the Roll)
  ```
- **U Cut Bags:**  
  ```
  (Net Weight of the Roll x 1550) / (Height of the Bag x Roll Size x GSM of the Roll)
  ```

---

## 4. Determining the Roll Size from the Bag Size  

- **D Cut Bags:**  
  ```
  Height of the Bag x 2 + 5/6
  ```
- **Loop Bags:**  
  ```
  Height of the Bag x 2 + 2/3/4
  ```
- **Box Bags:**  
  ```
  Height of the Bag x 2 + Gusset of the Bag + 2/3/4
  ```
- **U Cut Bags:**  
  ```
  Width of the Bag x 2 + 1/2
  ```

---

## 5. Calculating Bag Weight (in Grams)  

- **D Cut Bags:**  
  ```
  (Width x Roll Size x GSM) / 1550
  ```
- **Loop Bags:**  
  ```
  (Width x Roll Size x GSM) / 1550 + Loop Weight (3.4g)
  ```
- **U Cut Bags:**  
  ```
  ((Roll Size x Height x GSM) / 1550) – 10%
  ```
- **Box Bags:**  
  ```
  ((Width + Gusset) x Roll Size x GSM) / 1550 + Loop Weight (3.4g)
  ```

---

### 🔹 **Notes:**  
- Ensure all units are consistent when applying formulas.  
- Adjust calculations as needed based on material properties.  
- Double-check machine capabilities before scheduling.  

---

This guide ensures efficient roll utilization and optimal bag production planning. 🚀  

