import pandas as pd
import tensorflow as tf
from sklearn.preprocessing import StandardScaler

class ModelTester:
    def __init__(self, model_path, columns, scaler=None):
        self.model = tf.keras.models.load_model(model_path)
        self.columns = columns
        self.scaler = scaler or StandardScaler()

    def prepare_data(self, input_data):
        df = pd.DataFrame([input_data], columns=self.columns)
        if self.scaler:
            df[self.columns] = self.scaler.transform(df[self.columns])
        return df

    def predict(self, input_data):
        prepared_data = self.prepare_data(input_data)
        prediction = self.model.predict(prepared_data)
        return prediction[0]
